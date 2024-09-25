<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Categorie;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function all()
    {
        $services = Service::query()->oldest('title')->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $services
        ], 201);
    }

    public function show(string $slug)
    {
        $service = Service::query()->where('slug', $slug)->first();
        if (!$service === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service non retrouvée'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $service
        ], 201);
    }

    public function create(CreateServiceRequest $request)
    {
        $data = $request->validated();
        
        $categorie = Categorie::query()->where('id', $data['categorie_id'])->first();

        if ($categorie === null)
        {
            return response()->json([
                'status' => 'Error',
                'message' => 'Cette categorie est introuvable'
            ], 404);
        }
        
        try {
            
            $data['slug'] = Str::slug($data['title'], '-');

            $service = Service::query()->create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Enregistrement effectué',
                'data' => $service
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la creation du service', $e->getMessage());

            return response()->json([
                'status' => 'Error',
                'message' => 'Erreur lors de la creation du service',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    public function update(UpdateServiceRequest $request, string $slug)
    {
        $data = $request->validated();
        $service = Service::query()->where('slug', $slug)->first();
        $categorie = Categorie::query()->where('id', $data['categorie_id'])->first();

        if ($categorie === null)
        {
            return response()->json([
                'status' => 'Error',
                'message' => 'Cette categorie est introuvable'
            ], 404);
        }

        if ($service === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service non retrouvé'
            ], 404);
        }

        try {
            $service->slug = Str::slug($data['title'], '-');

            $service->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Modification effectuée',
                'data' => $service
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la modification du service', $e->getMessage());

            return response()->json([
                'status' => 'Error',
                'message' => 'Erreur lors de la modification du service',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    public function delete(int $id)
    {
        $service = Service::query()->where('id', $id)->first();
        if ($service === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service non retrouvé'
            ]);
        }
        
        try {
            $service->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Supression effectuée',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors du supression du service', $e->getMessage());

            return response()->json([
                'status' => 'Error',
                'message' => 'Erreur lors du supression du service',
                'error' => $e->getMessage()
            ], 401);
        }
    }
}
