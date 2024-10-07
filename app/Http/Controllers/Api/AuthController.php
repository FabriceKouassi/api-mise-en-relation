<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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

        $user->last_login = now();
        $user->save();
        
        return response()->json([
            'status' => 'success',
            'response' => [
                'token' => $user->createToken(Str::random(15))->plainTextToken,
                'status' => 'Utilisateur connecté',
                'user' => $user
            ]
        ], 201);
        
    }

    public function logout()
    {
        try {
            $user = Auth::user();
    
            $user->tokens()->delete();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Déconnexion réussie'
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la déconnexion', ['error' => $e->getMessage()]);
    
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la déconnexion'
            ], 500);
        }
    }

    protected function loginFail()
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Erreur de connexion, veuillez vous authentifié.'
        ], 401);
    }

}
