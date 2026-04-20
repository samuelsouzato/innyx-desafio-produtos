<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        // valida se o email e senha foram enviados na requisição
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            $result = $this->authService->login($request->all());
            // retorna o token e os dados do usuário para o frontend
            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 401);
        }
    }

    public function register(Request $request)
    {
        $messages = [
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Insira um e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso por outra conta.', 
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'As senhas digitadas não são iguais.',
        ];

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], $messages); 

        $user = $this->authService->register($validated);

        return response()->json([
            'message' => 'Usuário registrado com sucesso! Por favor, faça login.',
            'user'    => $user
        ], 201);
    }

    public function logout(Request $request)
    {
        // remove apenas o token que o usuário está usando no momento
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    public function destroy()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $this->authService->deleteAccount($user);

        return response()->json(['message' => 'Conta excluída com sucesso']);
    }
}