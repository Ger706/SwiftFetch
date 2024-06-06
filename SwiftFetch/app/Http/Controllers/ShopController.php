<?php

namespace App\Http\Controllers;

use App\Models\Shop;
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
                'address'
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

}
