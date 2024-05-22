<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function insertProduct(Request $request)
    {
        try{
            $data = [
                'product_name' => $request->product_name,
                'price' => $request->price,
                'quantity' => $request->quantity
            ];
            $data['created_at'] = now();
            $data['shop_id'] = $request->shopId;

            $product = Product::create($data);
        }
        catch (\Exception $e){
            throw $e;
        }

        return $this->showResponse('Product '.  $data['product_name'] . ' successfully created!');
    }
}
