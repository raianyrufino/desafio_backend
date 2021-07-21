<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseRequest;

class CreateRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|unique:users|email:rfc,dns',
            'password' => 'required|min:6',
            'cpf_cnpj' => 'required|unique:users',
            'type' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O campo Nome do Usuário é obrigatório',
            'email.required' => 'O campo E-mail do Usuário é obrigatório',
            'email.unique' => 'O  campo E-mail do Usuário deve ser único',
            'email.email' => 'O  campo E-mail do Usuário não é válido',
            'password.required' => 'O campo Senha deve possuir pelo menos 6 caracteres',
            'password.min' => 'O campo Senha é obrigatório',
            'cpf_cnpj.required' => 'O campo CPF/CNPJ é obrigatório',
            'cpf_cnpj.unique' => 'O campo CPF/CNPJ deve ser único',
            'type.required' => 'O campo Tipo é obrigatório',
        ];
    }
}
