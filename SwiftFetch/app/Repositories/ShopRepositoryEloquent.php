<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
use Mockery\Exception;

//use Your Model

/**
 * Class ShopRepository.
 */
class ShopRepositoryEloquent extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return Shop::class;
    }

    public function CreateShop($data) {
        try {
            DB::beginTransaction();
            $existShop = Shop::where('shop_name','=',$data['shop_name'])->get()->toArray();
            if (count($existShop) == 0) {
                Shop::create($data);
            } else {
                DB::rollBack();

            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return 1;
    }

    public function DeleteShop($shopId) {
        try {
            DB::beginTransaction();
            $shop = Shop::find($shopId);
            if ($shop) {
                $shopname = $shop->get()->toArray()['shop_name'];
                $shop->delete();
                DB::commit();
                return $shopname;
            } else {
                DB::rollBack();
                return 0;
            }

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function FindShopByUser($userId) {
        try {
            $shop = Shop::where('user_id','=',$userId)->whereNull('deleted_at')->get()->toArray();

            if (!$shop) {
                return 0;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $shop;
    }

    public function getProductByShop($shopId) {
        try {
            $product = Product::where('shop_id','=',$shopId)->whereNull('deleted_at')->get()->toArray();

            if (!$product) {
                return 0;
            }
            foreach($product as $item) {
                unset($item['created_at'],$item['updated_at'],$item['deleted_at']);
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $product;
    }
}
