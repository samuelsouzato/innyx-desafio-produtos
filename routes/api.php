<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rotas Públicas (Não precisa de token)
Route::post('/v1/login', [AuthController::class, 'login']);

// Rotas Protegidas (Só acessa se mandar o token válido)
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/v1/logout', [AuthController::class, 'logout']);

    Route::get('/v1/categories', [CategoryController::class, 'index']);
    
    Route::get('/v1/products', [ProductController::class, 'index']);

    Route::post('/v1/products', [ProductController::class, 'store']);
    
    Route::put('/v1/products/{id}', [ProductController::class, 'update']);
    
    Route::delete('/v1/products/{id}', [ProductController::class, 'destroy']);
});

