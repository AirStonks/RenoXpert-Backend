<?php

//  route/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MyController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::controller(AuthController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('credential/verify', 'isAuthenticated');
});
         
Route::middleware('auth:sanctum')->group( function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
    Route::get('/data', [MyController::class, 'getData']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('/products', ProductController::class);
    Route::apiResource('/product/category', ProductCategoryController::class);
});