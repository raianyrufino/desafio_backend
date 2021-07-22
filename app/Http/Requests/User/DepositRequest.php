<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseRequest;

class DepositRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'value' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'value.required' => 'O campo Valor é obrigatório',
        ];
    }
}
