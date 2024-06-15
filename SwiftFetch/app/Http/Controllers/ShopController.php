<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductTransaction;
use App\Models\Shop;
use App\Models\User;
use App\Repositories\ShopRepositoryEloquent;
use Illuminate\Http\Request;
use Mockery\Exception;

class ShopController extends Controller
{
    private $shopRepository;

    public function __construct(
        ShopRepositoryEloquent $shopRepository
    )
    {
        $this->shopRepository = $shopRepository;
    }

    function CreateShop(Request $req){
        try{
            $data = $req->only([
                'shop_name',
                'user_id',
                'address',
                'image',
                'description'
            ]);
            $data['created_at'] = now();
            $response = $this->shopRepository->CreateShop($data);

            if($response === 0) {
                return $this->showResponse(1, 'Shop '.  $data['shop_name'] . ' Already Exist');
            }
        } catch(Exception $e){
            throw $e;
        }
        return $this->showResponse(0, 'Shop '.  $data['shop_name'] . ' Successfully created ');
    }

    function DeleteShop(Request $req) {
        try{
            $shopId = $req['shop_id'];
            $response = $this->shopRepository->DeleteShop($shopId);

            if ($response === 0) {
                return $this->showResponse(1, 'Shop failed to be deleted');
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $this->showResponse(0, $response . ' Successfully deleted');
    }
    function FindShopByUser(Request $req) {
        try{
            $userId = $req['user_id'];
            $response = $this->shopRepository->FindShopByUser($userId);

            if ($response === 0) {
                return $this->showResponse(1, 'No Shop Found');
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $response;
    }

    function getShopProduct($shopId) {
        try {
            $response = $this->shopRepository->getProductByShop($shopId);

            if ($response === 0) {
                return $this->showResponse(1, 'No Product Found');
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $response;
    }

    function getShopInfo($shopId) {

        try{
            $response = $this->shopRepository->getShopInfo($shopId);

            if ($response === null){
                return $this->showResponse(1, 'No Shop Information Found');
            }
        } catch (Exception $e){
            throw $e;
        }
        return $response;
    }

    function getShopByProductId($productId) {
        try {
            $shopId = $this->shopRepository->getShopByProduct($productId);

            $shop = Shop::find($shopId);
            if (!isset($shop)) {
                return null;
            }
        } catch (Exception $e){
            throw $e;
        }
        return $shop;
    }

    function getShopOrder($shopId) {
        try{
            $order = ProductTransaction::where('shop_id','=',$shopId)->whereNull('deleted_at')
                ->orderBy('id', 'desc')
                ->get()->toArray();
            if(count($order) < 1){
                return $this->showResponse(1, "No Transaction made at this point");
            }
            foreach ($order as $index => $item) {
                $product = Product::where('id','=',$item['product_id'])->first();
                $shop = Shop::where('id','=',$item['shop_id'])->first();
                $user = User::where('id','=',$item['user_id'])->first();
                $order[$index]['product_name'] = $product->product_name;
                $order[$index]['shop_name'] = $shop->shop_name;
                $order[$index]['shop_image'] = $shop->image;
                $order[$index]['product_image'] = $product->image;
                $order[$index]['remaining'] = $product->remaining_stock;
                $order[$index]['user_name'] = $user->name;
                $order[$index]['user_image'] = $user->photo;

                unset($order[$index]['created_at'], $order[$index]['deleted_at'], $order[$index]['updated_at']);
            }
        } catch (Exception $e){
            throw $e;
        }
        return $order;
    }
}
