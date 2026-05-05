<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LocalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome'      => 'required|string|max:255',
            'descricao' => 'nullable|string|max:500',
            'ativo'     => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome do local é obrigatório.',
            'nome.max'      => 'O nome deve ter no máximo 255 caracteres.',
            'descricao.max' => 'A descrição deve ter no máximo 500 caracteres.',
            'ativo.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
        ];
    }
}