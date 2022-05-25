<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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
Route::post('/login', [LoginController::class, 'authenticate']);

Route::group(['middleware' => ['jwt.verify']], function() {

    Route::resources(['user' => UserController::class]);

    Route::resources(['customer' => CustomerController::class]);

    Route::resources(['product' => ProductController::class]);

    Route::post('/user/lock-or-unlock', [UserController::class, 'lockOrUnlockUser']);

});



