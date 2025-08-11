<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'per_page' => [
                'required',
                'numeric',
                'min:1',
                'max:100',
            ],
            'page' => [
                'required',
                'numeric',
                'min:1',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'per_page.required' => 'O campo "per_page" é obrigatório.',
            'per_page.numeric' => 'O campo "per_page" deve ser um número.',
            'per_page.min' => 'O campo "per_page" deve ser no mínimo :min.',
            'per_page.max' => 'O campo "per_page" deve ser no máximo :max.',

            'page.required' => 'O campo "page" é obrigatório.',
            'page.numeric' => 'O campo "page" deve ser um número.',
            'page.min' => 'O campo "page" deve ser no mínimo :min.',
        ];
    }
}
