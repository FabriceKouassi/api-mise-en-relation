<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCategorieRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'libelle' => 'required|unique:categories,libelle|string',
            'slug' => 'nullable',
            'description' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'libelle.required' => 'Le libelle est obligatoire',
            'libelle.string' => 'Le libelle dois être une chaine de caractères',
            'libelle.unique' => 'Ce libelle existe déjà',
            'description.string' => 'La description dois être une chaine de caractères',
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
