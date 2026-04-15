<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Valida se o usuário enviou email e senha
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 2. Tenta fazer o login com os dados fornecidos
        if (Auth::attempt($credentials)) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            // 3. Cria o token de acesso (Sanctum)
            $token = $user->createToken('auth_token')->plainTextToken;

            // 4. Devolve o token para o Front-end (Vue.js) guardar
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }

        // Se a senha estiver errada, devolve erro 401 (Não autorizado)
        return response()->json(['message' => 'Credenciais inválidas'], 401);
    }

    public function logout(Request $request)
    {
        // Pega o usuário logado e deleta o token atual dele
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso']);
    }
}