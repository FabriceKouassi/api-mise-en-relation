<?php

namespace App\Http\Requests;

use App\Http\Enums\Roles;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateUserRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if (is_string($this->input('services'))) {
            $this->merge([
                'services' => json_decode($this->input('services'), true),
            ]);
        }
        
        $rules = [
            'lastName' => 'required|string',
            'firstName' => 'required|string',
            'slug' => 'nullable|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'phone' => 'required|unique:users,phone',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|max:18|min:8',
            'role' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, Roles::all())) {
                        $fail('Le rôle sélectionné est invalide');
                    }
                },
            ],
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
        ];
        // Règles basées sur le rôle
        if ($this->input('role') === 'prestataire') {
            $rules['services'] = 'required|array|min:1'; // Rendre les services obligatoires pour les prestataires
            $rules['services.*'] = 'exists:services,id'; // Vérifiez que chaque service existe
            // dd($this->input('role'));
        } elseif ($this->input('role') === 'demandeur') {
            $rules['services'] = 'nullable|array'; // Services sont optionnels pour les demandeurs
        }

        return $rules;
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
            
            'services.required' => 'Les services sont requis pour les prestataires.',
            'services.array' => 'Les services doivent être un tableau.',
            'services.min' => 'Vous devez sélectionner au moins un service pour les prestataires.',
            'services.*.exists' => 'Chaque identifiant de service doit exister dans la base de données.',
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
