<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Enums\Roles;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function all()
    {
        $users = User::query()->latest()->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $users,
        ], 200);
    }

    public function show(string $slug)
    {
        $user = User::query()->where('slug', $slug)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Utilisateur non retrouvé'
            ]);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $user
        ], 201);
    }

    public function register(CreateUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $lastName = $data['lastName'] ?? '';
        $firstName = $data['firstName'] ?? '';

        $user = User::query()->create($data);
        $user->slug = Str::slug($lastName . '-' . $firstName . '-' . time(), '-');

        if ($data['role'] === Roles::PRESTATAIRE && $request->has('services'))
        {
            if (is_array($data['services'])) {
                $user->services()->attach($data['services']);
            } else {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Les services doivent être un tableau d\'identifiants valides.'
                ], 400);
            }
        }
        
        if($request->hasFile('img')){

            try {
                $img = $request->file('img');
                $filename = time() . '-' . $img->getClientOriginalName();

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

    public function update(UpdateUserRequest $request, string $slug)
    {
        try {
            $user = User::query()->where('slug', $slug)->first();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Utilisateur non retrouvé'
                ]);
            }

            $data = $request->validated();

            $lastName = $data['lastName'] ?? '';
            $firstName = $data['firstName'] ?? '';
            $userId = $user->id ?? '';

            $user->slug = Str::slug($lastName . '-' . $firstName . '-' . $userId, '-');
            if($request->hasFile('img')){
            
                try {

                    unlink(storage_path(config('global.user_image').'/'.$user->img));

                    $img = $request->file('img');
                    $filename = time() . '-' . $img->getClientOriginalName();
    
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
            
            $user->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Modification effectuée',
                'data' => $user
            ], 201);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur s\'est produite lors de la modification',
                'error' => $e->getMessage()
            ], 422);
        }
        
    }

    public function delete (string $slug)
    {
        $user = User::query()->where('slug', $slug)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Utilisateur non retrouvé'
            ]);
        }

        try {

            $user->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Suppression effectuée'
            ], 200);

        } catch (\Exception $e) {

            Log::error('Erreur lors de la suppression de l\'utilisateur' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur s\'est produite lors de la suppression',
                'error' => $e->getMessage()
            ], 401);

        }
    }
}
