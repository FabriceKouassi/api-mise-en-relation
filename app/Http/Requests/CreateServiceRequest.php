<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|unique:services,title|string',
            'slug' => 'nullable',
            'description' => 'nullable|string',
            'categorie_id' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Le titre est obligatoire',
            'title.string' => 'Le titre dois être une chaine de caractères',
            'title.unique' => 'Ce service existe déjà',
            'description.string' => 'La description dois être une chaine de caractères',
            'categorie_id.required' => 'L\'id de la categorie est obligatoire',
            'categorie_id.integer' => 'L\'id de la categorie dois être un entier',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status' => 'failed',
            'response' => $validator->errors()
        ], 422));
    }
}
