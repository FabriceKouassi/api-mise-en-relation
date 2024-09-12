<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return false;
    }

    public function rules(): array
    {
        return [
            'lastName' => 'required|string',
            'firstName' => 'required|string',
            'phone' => 'required|unique:users,phone',
            'email' => 'sometimes|email|unique:users,email',
            'role' => 'required',
            'password' => 'required|max:18|min:8',
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
            'phone.unique' => 'Ce contact est déjà enregistré',
            'role.required' => 'Le role est obligatoire',
            'password.required' => 'Le mot de passe est obligatoire',
            'password.max' => 'Le mot de passe ne dois pas exédé 18 caractères',
            'password.min' => 'Le mot de passe ne dois pas être en dessous de 8 caractères',
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
