<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::middleware('auth:api')->get('/test', function (Request $request) {
   return response()->json([
        "statusCode" => true
    ]);

});
Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('/test', [UserController::class, 'test']);
    Route::resources([
        'user' => UserController::class
    ]);
    Route::post('/user/lock-or-unlock', [UserController::class, 'lockOrUnlockUser']);
});



