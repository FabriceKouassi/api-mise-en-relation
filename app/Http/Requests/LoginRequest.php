<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => 'required',
            'password' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'login' => 'Vos accès sont incorrect veuillez réessayer !',
            'password' => 'Vos accès sont incorrect veuillez réessayer !',
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
