<?php

use App\Http\Controllers\ProductController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    //product api
   /*  if (!$request->user()->tokenCan('role:addproduct')) {
        return [
            "success" => false,
            "message" => "UnAuthorised Role for user"
        ];
    } else { */
        Route::get('/product', [ProductController::class, 'index']);
        Route::get('/product/{id}', [ProductController::class, 'show']);
        Route::post('/product/add', [ProductController::class, 'add']);
        Route::post('/product/{id}', [ProductController::class, 'edit']);
        Route::delete('/product/{id}', [ProductController::class, 'delete']);
    
});



Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
