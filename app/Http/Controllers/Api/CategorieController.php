<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategorieRequest;
use App\Http\Requests\UpdateCategorieRequest;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategorieController extends Controller
{
    public function all()
    {
        $categories = Categorie::query()->oldest('libelle')->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ], 201);
    }

    public function create(CreateCategorieRequest $request)
    {
        $data = $request->validated();

        try {
            
            $data['slug'] = Str::slug($data['libelle'], '-');

            $categorie = Categorie::query()->create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Enregistrement effectué',
                'data' => $categorie
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la creation de la categorie', $e->getMessage());

            return response()->json([
                'status' => 'Error',
                'message' => 'Erreur lors de la creation de la categorie',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    public function update(UpdateCategorieRequest $request, string $slug)
    {
        $data = $request->validated();
        $categorie = Categorie::query()->where('slug', $slug)->first();
        
        if (!$categorie) {
            return response()->json([
                'status' => 'error',
                'message' => 'Catégorie non retrouvée'
            ]);
        }

        try {
            $categorie->slug = Str::slug($data['libelle'], '-');

            $categorie->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Modification effectuée',
                'data' => $categorie
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la modification de la categorie', $e->getMessage());

            return response()->json([
                'status' => 'Error',
                'message' => 'Erreur lors de la modification de la categorie',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    public function delete(int $id)
    {
        $categorie = Categorie::query()->where('id', $id)->first();
        
        if (!$categorie) {
            return response()->json([
                'status' => 'error',
                'message' => 'Catégorie non retrouvée'
            ]);
        }
        
        try {
            $categorie->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Supression effectuée',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la supression de la categorie', $e->getMessage());

            return response()->json([
                'status' => 'Error',
                'message' => 'Erreur lors de la supression de la categorie',
                'error' => $e->getMessage()
            ], 401);
        }
    }

}
