<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
}
