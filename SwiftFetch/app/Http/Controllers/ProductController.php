<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Repositories\ProductRepositoryEloquent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class ProductController extends Controller
{
    public $productRepository;
    public function __construct(
        ProductRepositoryEloquent $productRepository
    )
    {
        $this->productRepository = $productRepository;
    }

    public function insertProduct(Request $req){
        try{
            DB::begintransaction();
            $data = $req->only([
                'product_name',
                'price',
                'shop_id',
                'detail',
                'quantity',
                'origin',
                'image'
            ]);

            $data['created_at'] = now();
            $data['remaining_stock'] = $data['quantity'];

            Product::create($data);
            DB::commit();
        }catch(\Exception $e)
        {
            throw $e;
        }
        return $this->showResponse(0, 'Succesfully create a new product');
    }

    public function deleteProduct(Request $req)
{
    DB::beginTransaction();
    try{
        $data = $req->only([
            'product_id'
        ]);

        $productData = Product::find($data['product_id']);

        if(isset($productData))
        {
            $productData->deleted_at = now();
            $productData->save();
            DB::commit();
        }
        else{
            return $this->showResponse(1, 'Product not found');
        }

    }catch (\Exception $e)
    {
        throw $e;
    }
    return $this->showResponse(0, 'Succesfully delete a product');
}

    public function editProduct(Request $req)
    {
        DB::beginTransaction();
        try{
            $data = $req->only([
                'product_id',
                'product_name',
                'price',
                'remaining_stock',
                'detail',
                'image'
            ]);

            $productData = Product::find($data['product_id']);

            if(isset($productData))
            {
                $productData->fill($data);
                $productData->save();
                DB::commit();
            }
            else{
                return $this->showResponse(1, 'Product not found');
            }

        }catch (\Exception $e)
        {
            throw $e;
        }
        return $this->showResponse(0, 'Succesfully edited a product');
    }

    public function getRandomProduct(Request $req) {
        try {
            $data = $req->only([
                'search',
                'sortBy',
                'user_id'
            ]);

            $product = Product::
            join('shop','shop.id','=','product.shop_id')
                ->where('shop.user_id','!=',$data['user_id'])
                ->select('product.*')
            ->whereNull('product.deleted_at')
                ->where('remaining_stock','>', 0);

            $query = $data['search'];

            if(isset($query))
            {
                $product = $product->where('product_name', 'LIKE', "%{$query}%");
            }
            if($data['sortBy'] === "orderAsc") {

                $product = $product->orderBy('price');
            } elseif($data['sortBy'] === "orderDesc") {
                $product = $product->orderByDesc('price');
            } else {
                $product = $product->inRandomOrder();
            }

            $product = $product->paginate(15)->items();

            foreach($product as $index => $item) {
                unset($product[$index]['created_at'], $product[$index]['deleted_at'], $product[$index]['updated_at']);
            }
            if (count($product) < 1) {
                return $this->showResponse(1, 'There is no product in the meantime');
            }

        } catch (Exception $e) {
            throw $e;
        }
        return $product;
    }

    public function getProductDetail($productId){
        try {
            $productDetail = $this->productRepository->getDetail($productId);
            if ($productDetail === 1){
                return $this->showResponse('There is an error finding the product detail');
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $productDetail;
    }

    public function getRecommendedProduct(Request $req){
        try{
            $param = $req->only(
                'user_id'
            );

            $product = Product::
            join('shop','shop.id','=','product.shop_id')
                ->where('shop.user_id','!=',$param['user_id'])
                ->select('product.*')
            ->paginate(8)->whereNull('deleted_at')->where('remaining_stock','>', 0)->toArray();
            foreach($product as $index => $item){
                if($item['sold'] != 0){
                    $product[$index]['rate'] = $item['quantity'] / $item['sold'] * 100;
                } else {
                    unset($product[$index]);
                }
            }
            usort($product, function ($a, $b) {
                return $a['rate'] <=> $b['rate'];
            });
        } catch(Exception $e){
            throw $e;
        }
        return $product;
    }

    public function getProductUnder(Request $req){
        try{
            $param = $req->only(
                'price',
                'user_id'
            );
            $product = Product::
            join('shop','shop.id','=','product.shop_id')
                ->where('shop.user_id','!=',$param['user_id'])
                ->select('product.*')
            ->paginate(8)->whereNull('deleted_at')->where('price','<=',$param['price'])->where('remaining_stock','>', 0)->toArray();
            usort($product, function ($a, $b) {
                return $a['price'] <=> $b['price'];
            });
        } catch(Exception $e){
            throw $e;
        }
        return $product;
    }
}
