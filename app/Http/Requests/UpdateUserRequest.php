<?php

namespace App\Http\Requests;

use App\Http\Enums\Roles;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lastName' => 'required|string',
            'firstName' => 'required|string',
            'slug' => 'nullable',
            'img' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'phone' => 'sometimes|unique:users,phone',
            'email' => 'sometimes|nullable|email|unique:users,email',
            'password' => 'sometimes|max:18|min:8',
            'role' => ['sometimes', 'string', function ($attribute, $value, $fail) {
                if (!in_array($value, Roles::all())) {
                    $fail('Le role selectionné est invalide');
                }
            }],
        ];
    }

    public function messages()
    {
        return [
            'lastName.required' => 'Le nom est obligatoire',
            'lastName.string' => 'Le nom dois être une chaine de caractères',
            'firstName.required' => 'Le prenoms est obligatoire',
            'firstName.string' => 'Le prenoms dois être une chaine de caractères',
            'phone.required' => 'Le contact est obligatoire',
            'phone.unique' => 'Ce contact existe déjà',
            'email.unique' => 'Ce email existe déjà',
            'email.email' => 'Veuillez saisir un email valide',
            'role.required' => 'Le role est obligatoire',
            'password.required' => 'Le mot de passe est obligatoire',
            'password.max' => 'Le mot de passe ne dois pas exédé 18 caractères',
            'password.min' => 'Le mot de passe ne dois pas être en dessous de 8 caractères',
            'img.image' => 'Une image est obligatoire',
            'img.mimes' => 'L\'image choisie dois être de type: jpeg, png, jpg, gif ou svg',
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
