<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rotas Públicas (Não precisa de token)
Route::post('/v1/login', [AuthController::class, 'login']);

// Rotas Protegidas (Só acessa se mandar o token válido)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/v1/logout', [AuthController::class, 'logout']);
    
    // Nossas futuras rotas de Produtos e Categorias vão entrar aqui dentro!
});