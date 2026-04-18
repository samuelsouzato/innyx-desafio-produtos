<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'E-mail ou senha incorretos.'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user 
        ]);
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

    $user = User::create([
        'name'     => $validated['name'],
        'email'    => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    return response()->json([
        'message' => 'Usuário registrado com sucesso! Por favor, faça login.',
        'user'    => $user
    ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    public function destroy()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        // Deletar arquivos físicos das imagens
        foreach ($user->products as $product) {
            if ($product->getRawOriginal('image')) {
                Storage::disk('public')->delete($product->getRawOriginal('image'));
            }
        }

        $user->delete();

        return response()->json(['message' => 'Conta excluída com sucesso']);
    }
}