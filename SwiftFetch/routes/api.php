<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductTransactionController;

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

        #Change Password
        Route::post('/change-password','changePassword');

        Route::post('/top-up','topUpBalance');
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
Route::controller(ShopController::class)->group(function () {

    Route::group(['prefix' => 'shop'], function () {
        # Create Shop
        Route::post('/create-shop','CreateShop');

        # Delete Shop
        Route::delete('/delete-shop', 'DeleteShop');

        # Find Shop By User Id
        Route::post('/find-shop-by-id','FindShopByUser');

        # Get Shop Product
        Route::get('/{shopId}', 'getShopProduct');

        Route::get('/get-shop-info/{shopId}', 'getShopInfo');

        Route::get('/get-shop-by-product/{productId}','getShopByProductId');

        Route::get('/get-shop-order/{shopId}','getShopOrder');

    });

});

# -- Cart
Route::controller(CartController::class)->group(function () {

    Route::group(['prefix' => 'cart'], function () {
        # Create Cart
        Route::post('/create-cart','CreateCart');

        # Delete Cart
        Route::post('/delete-cart', 'DeleteCart');

        # Find Cart By User Id
        Route::get('/{userId}','getCart');
    });

});

# -- Product
Route::controller(ProductController::class)->group(function() {
    Route::group(['prefix' => 'product'], function (){
        # Insert Product
        Route::post('/insert-product', 'insertProduct');

        Route::post('/delete-product', 'deleteProduct');

        Route::post('/edit-product', 'editProduct');

        Route::post('/get-random-product', 'getRandomProduct');

        Route::post('/get-recommended-product', 'getRecommendedProduct');

        Route::get('/{productId}','getProductDetail');

        Route::post('/get-price-under','getProductUnder');
    });
});

Route::controller(ProfileController::class)->group(function() {
    Route::group(['prefix' => 'profile'], function (){

        Route::get('/{userId}', 'getProfile');

        Route::post('/edit-profile', 'editProfile');
    });
});

Route::controller(ProductTransactionController::class)->group(function() {
    Route::group(['prefix' => 'transaction'], function (){

        Route::post('/make-transaction', 'makeTransaction');

        Route::get('/get-transaction/{userId}', 'getTransaction');

        Route::post('/get-bill','getBill');

        Route::post('/change-status','changeStatus');

        Route::post('/cancel-order','cancelOrder');
    });
});



