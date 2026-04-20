<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Exception;

class AuthService
{
    // Lógica para autenticar o usuário e gerar o token
    public function login(array $data)
    {
        // busca o usuário no banco pelo email
        $user = User::where('email', $data['email'])->first();

        // checa se o usuário existe e se a senha enviada bate com a hash salva
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new Exception('E-mail ou senha incorretos.', 401);
        }

        // gera o token de acesso (Sanctum)
        $token = $user->createToken('auth_token')->plainTextToken;

        // retorna o token e os dados do usuário formatados
        return [
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user
        ];
    }

    // Lógica para criar um novo usuário no banco
    public function register(array $data)
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    // Lógica para limpar dados e excluir a conta
    public function deleteAccount(User $user)
    {
        // Loop para deletar fisicamente as imagens de cada produto do usuário no servidor
        foreach ($user->products as $product) {
            if ($product->getRawOriginal('image')) {
                Storage::disk('public')->delete($product->getRawOriginal('image'));
            }
        }

        // Deleta o registro do usuário (as relações são tratadas pelo banco ou Cascade)
        return $user->delete();
    }
}