<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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
    });

});

