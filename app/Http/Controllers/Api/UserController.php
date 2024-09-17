<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
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
            ], 200);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur s\'est produite lors de la modification',
                'error' => $e->getMessage()
            ], 422);
        }
        
    }
}
