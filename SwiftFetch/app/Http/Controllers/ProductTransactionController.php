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
                'real_price'
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

    public function getTransaction($userId){
        try {
            $transaction = ProductTransaction::where('user_id','=',$userId)->whereNull('deleted_at')
             ->orderBy('id', 'desc')
            ->get()->toArray();
            if(count($transaction) < 1){
                return $this->showResponse(1, "No Transaction made at this point");
            }
            foreach ($transaction as $index => $item) {
                $product = Product::where('id','=',$item['product_id'])->first();
                $shop = Shop::where('id','=',$item['shop_id'])->first();
                $transaction[$index]['product_name'] = $product->product_name;
                $transaction[$index]['shop_name'] = $shop->shop_name;
                $transaction[$index]['shop_image'] = $shop->image;
                $transaction[$index]['product_image'] = $product->image;
                $transaction[$index]['remaining'] = $product->remaining_stock;
                unset($transaction[$index]['created_at'], $transaction[$index]['deleted_at'], $transaction[$index]['updated_at']);
            }
        } catch (Exception $e){
            throw $e;
        }

        return $transaction;
    }

    public function changeStatus(Request $req){
        try {
            $param = $req->only(
                'transaction_id',
                'status',
                'seller_done'
            );

           $transaction =  ProductTransaction::find($param['transaction_id']);
           if($param['status'] === "Done" && $param['seller_done'] === 1){
               $transaction->seller_done = 1;
           } else if ($param['status'] === "Done" && $param['seller_done'] === 0) {
               $transaction->status = "Done";
               $product = ProductTransaction::find($transaction->product_id);
               $product->sold = $product->sold + $transaction->quantity;

               $shop = Shop::find($product->shop_id);
               $user = User::find($shop->user_id);

               $user->balance = $user->balance + $transaction->real_price - ($transaction->real_price * (5/100));
               $user->save();
               $product->save();
           } else {
               $transaction->status = $param['status'];
           }

        }  catch (Exception $e) {
            throw $e;
        }
        return $this->showResponse(0,"Status Successfully changed");
    }
}
