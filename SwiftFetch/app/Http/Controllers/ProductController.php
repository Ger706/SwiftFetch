<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
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
        return $this->showRespone(0, 'Succesfully create a new product');
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
                return $this->showResponse(0, 'Product not found');
            }

        }catch (\Exception $e)
        {
            throw $e;
        }
        return $this->showRespone(0, 'Succesfully delete a product');
    }
}
