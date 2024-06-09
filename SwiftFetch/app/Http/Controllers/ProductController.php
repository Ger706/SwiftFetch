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
                'origin'
            ]);

            $data['created_at'] = now();

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

            $productData = Product::where('id', $data['product_id'])->first();

            if(isset($productData))
            {
                $productData->delete();
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

    public function getRandomProduct(Request $req) {
        try {
            $data = $req->only([
                'search',
                'sortBy'
            ]);

            $product = Product::whereNull('deleted_at');

            $query = $data['search'];

            if(isset($query))
            {
                $product = $product->where('product_name', 'LIKE', "%{$query}%");
            }

            if($data['sortBy'] === "orderAsc") {
                $product = $product->sortBy('price');
            } elseif($data['sortBy'] === "orderDesc") {
                $product = $product->sortByDesc('price');
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

    public function getRecommendedProduct(){
        try{
            $product = Product::paginate(8)->whereNull('deleted_at')->toArray();
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
}
