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

        $user = User::query()->create($credentials);

        $lastName = $user->lastName ?? '';
        $firstName = $user->firstName ?? '';
        $userId = $user->id ?? '';

        $user->slug = Str::slug($lastName . '-' . $firstName . '-' . $userId, '-');

        if($request->hasFile('img')){
            
            try {
                $img = $request->file('img');
                $filename = time() . '-' . $img->getClientOriginalName();
                // $img->move(storage_path(config('global.user_image'), $filename));
                // $img->move(public_path('storage/'). config('global.user_image'), $filename);

                $img->storeAs(config('global.user_image'), $filename, 'public');

                $user->img = $filename;

            } catch (\Exception $e) {

                Log::error('Erreur lors du traitement de l\'image', ['error' => $e->getMessage()]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Erreur lors du traitement de l\'image',
                ], 500);

            }
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Utilisateur enregistré',
            'data' => $user,
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
