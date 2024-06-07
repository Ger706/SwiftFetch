<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductTransaction;
use App\Models\Shop;
use App\Models\User;
use App\Repositories\ShopRepositoryEloquent;
use Illuminate\Http\Request;
use Mockery\Exception;

class ProductTransactionController extends Controller
{
    public $shopRepository;
    public function __construct(
        ShopRepositoryEloquent $shopRepository
    ){
        $this->shopRepository = $shopRepository;
    }
    public function makeTransaction(Request $req){


        try {
            $param = $req->only(
                'product_id',
                'user_id',
                'quantity',
                'payment',
            );
            $param['status'] = "Pending";
            $param['shop_id'] = $this->shopRepository->getShopByProduct($param['product_id']);


            if($param['shop_id'] !== null) {
                $param['shop_id'] = $param['shop_id']->first();

                $user = User::find($param['user_id']);
               if($user){
                   if($user->balance >= $param['payment']){
                        $user->balance = $user->balance - $param['payment'];
                       ProductTransaction::create($param);
                       $user->save();

                       $shop = Product::find($param['product_id']);
                       $shop->remaining_stock = $shop->remaining_stock - $param['quantity'];
                       $shop->save();
                   } else {
                       return $this->showResponse(1,'Balance is not enough');
                   }
               }

            } else {
                return $this->showResponse(1,'Failed to make transaction');
            }


        } catch(Exception $e){
            throw $e;
        }
        return $this->showResponse(0,'Transaction Successfully made');
    }

    public function getBill(Request $req){
        try {
            $param = $req->only(
                'address',
                'product_id',
                'quantity'
            );

           $shopId = $this->shopRepository->getShopByProduct($param['product_id']);

            if($shopId !== null) {
                $shopId = $shopId->first();
                $shop = $this->shopRepository->getShopInfo(collect($shopId));
                if($shop !== null){
                    $shopAddress = $shop[0]['address'];
                }else{
                    $shopAddress = null;
                }
                $price = Product::getProduct($param['product_id']);
                if($price){
                    $price = $price->first()['price'];
                    $cities = ['Jakarta', 'Depok', 'Bekasi', 'Tangerang', 'Bogor'];
                    $CityTo = null;
                    foreach ($cities as $city) {
                        if (stripos($param['address'], $city) !== false) {
                            $CityTo = $city;
                            break;
                        }
                    }
                    $CityFrom = null;
                    foreach ($cities as $city) {
                        if (stripos($shopAddress, $city) !== false) {
                            $CityFrom = $city;
                            break;
                        }
                    }
                    if($CityTo == $CityFrom) {
                        $fee = 15000;
                    } else if($CityTo === null || $CityFrom === null) {
                        $fee = 40000;
                    } else {
                        $fee = 25000;
                    }
                    $productPrice = $param['quantity'] * $price;
                    $tax =  $productPrice/100 + $fee/100;
                    return [
                        "fee" => $fee,
                        "service" => $tax,
                        "price" => $productPrice,
                        "grand_total" => $fee+$tax+$productPrice
                    ];
                } else {
                    return $this->showResponse(1,'Failed to get Price');
                }
            } else {
                return $this->showResponse(0,'Failed to get Price');
            }
        } catch(Exception $e) {
            throw $e;
        }
    }
}
