<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;

Route::get('/{any?}', function () {
    return view('app'); 
})->where('any', '.*');
// Agrupamos tudo dentro do prefixo 'v1'
Route::prefix('v1')->group(function () {

    // --- Rotas Públicas ---
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    // --- Rotas Protegidas ---
    Route::middleware('auth:sanctum')->group(function () {
        
        // Autenticação e Usuário
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::delete('/user', [AuthController::class, 'destroy']); // Excluir conta
        Route::get('/me', function (Request $request) { // Rota para pegar dados do user logado
            return $request->user();
        });

        // Categorias
        Route::get('/categories', [CategoryController::class, 'index']);
        
        // Produtos (CRUD completo)
        Route::get('/products', [ProductController::class, 'index']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    });
});