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
class ProductRepositoryEloquent extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return Product::class;
    }

    public function getDetail($productId){
        try {
            $product = Product::getProduct($productId)->first();
            if (!isset($product)){
                return 1;
            }
            unset($product['created_at'], $product['deleted_at'], $product['updated_at']);
        } catch (Exception $e) {
            throw $e;
        }
        return $product;
    }
}
