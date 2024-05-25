<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SellerController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

# --- Authentication
Route::controller(AuthController::class)->group(function () {

    Route::group(['prefix' => 'auth'], function () {
        # Register
        Route::post('/create-user','CreateUser');

        #Login
        Route::post('/login','Login');

        #Register As Seller
        Route::post('/register-seller','RegisterAsSeller');
    });

});

# --- Seller
Route::controller(SellerController::class)->group(function () {

    Route::group(['prefix' => 'seller'], function () {
        #Register As Seller
        Route::post('/register-seller','RegisterAsSeller');
    });
});

# --- Shop
Route::controller(Shopcontroller::class)->group(function () {

    Route::group(['prefix' => 'shop'], function () {
        # Create Shop
        Route::post('/create-shop','CreateShop');

        # Delete Shop
        Route::delete('/delete-shop', 'DeleteShop');

        # Find Shop By User Id
        Route::post('/find-shop-by-id','FindShopByUser');
    });

});

