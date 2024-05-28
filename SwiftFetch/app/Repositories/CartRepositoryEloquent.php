<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
use Mockery\Exception;

//use Your Model

/**
 * Class CartRepository.
 */
class CartRepositoryEloquent extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return Cart::class;
    }

    public function InsertCart($data) {
        try {
            DB::beginTransaction();
            $existCart = Cart::where('product_id','=',$data['product_id'])
                ->where('user_id','=',$data['user_id'])
                ->first();
            if (isset($existCart)) {
                $existCart->quantity += $data['quantity'];
                $existCart->save();
            } else {
                Cart::create($data);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function DeleteFromCart($data) {
        try {
            DB::beginTransaction();
            $cart = Cart::find($data['cart_id'])->first();
            if (isset($cart)) {
                $cart->delete();
                DB::commit();
            } else {
                DB::rollBack();
                return 'Cart does not exist';
            }
            if($data['from'] === 'cart'){
                return 'Cart Successfully Removed';
            } else if ($data['from'] === 'checkout') {
                return 'Cart Successfully Checkout';
            }

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return 0;
    }

    public function getCartByUser($userId) {
        try {
            $cart = Cart::where('user_id','=',$userId)->get()->toArray();
            if (!$cart) {
                return 0;
            }
            foreach($cart as $index => $cartData){
                $cart[$index]['price'] = Product::getPrice($cartData['product_id']) * $cartData['quantity'];
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $cart;
    }
}
