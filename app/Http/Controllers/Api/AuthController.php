<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $user = User::query()
                    ->where('email', $credentials['login'])
                    ->orWhere('phone', $credentials['login'])
                    ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'status' => 'failed',
                'response' => 'Email ou mot de passe incorrect'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'response' => [
                'token' => $user->createToken(Str::random(15))->plainTextToken,
                'status' => 'Utilisateur connecté',
                'user' => $user
            ]
        ], 201);
        
    }

    public function register(RegisterRequest $request)
    {
        $credentials = $request->validated();
        $credentials['password'] = Hash::make($credentials['password']);

        User::query()->create($credentials);

        return response()->json([
            'status' => 'success',
            'message' => 'Utilisateur enregistré',
        ], 201);
    }


}
