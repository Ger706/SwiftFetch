<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Repositories\CartRepositoryEloquent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class CartController extends Controller
{
    public $cartRepository;

    public function __construct(
        CartRepositoryEloquent $cartRepository
    )
    {
        $this->cartRepository = $cartRepository;
    }

    public function CreateCart(Request $req)
    {
        try {
            $data = $req->only([
                'user_id',
                'product_id',
                'quantity',
            ]);
            $data['created_at'] = now();
            $this->cartRepository->InsertCart($data);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return $this->showResponse(0,'Product successfully added to cart');
    }
    public function DeleteCart(Request $req)
    {
        try {
            $data = $req->only([
                'cart_id',
                'from'
            ]);
            $result = $this->cartRepository->DeleteFromCart($data);
            if ($result === 0){
                return $this->showResponse(1,'Failed to remove cart');
            }
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return $this->showResponse(0,$result);
    }

    public function getCart($userId){
        try {
            $result = $this->cartRepository->getCartByUser($userId);
            if ($result == 0) {
                return $this->showResponse(1, 'Cart is Empty');
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $result;
    }
}
