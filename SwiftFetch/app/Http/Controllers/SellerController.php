<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\User;
use App\Repositories\ShopRepositoryEloquent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class SellerController extends Controller
{
    public $shopRepository;
    public function __construct(
        ShopRepositoryEloquent  $shopRepository
    )
    {
        $this->shopRepository = $shopRepository;
    }

    public function RegisterAsSeller(Request $req) {
        try {
            DB::beginTransaction();
            $data = $req->only([
                'user_id',
                'subscribe'
            ]);
            $user = User::find($data['user_id']);
            $userData = $user->first();
            if (!isset($userData)){
                $this->showResponse(1,'No User Found');
            }
            if ($data['subscribe'] === true && isset($userData)){
                $user->is_seller = 1;
            } else if ($data['subscribe'] === false && isset($userData)) {
                $user->is_seller = 0;
                $shopId = $this->shopRepository->FindShopByUser($data['user_id']);
                $this->shopRepository->DeleteShop($shopId[0]['id']);
                DB::commit();
                return $this->showResponse(0,'Successfully Unregister as Seller');
            }
            $user->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return $this->showResponse(0,'Successfully Registered as Seller');
    }
}
