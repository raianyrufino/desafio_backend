<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseRequest;

class TransferRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'value' => 'required|regex:/^\d*(\.\d{2})?$/',
            'payee_id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'value.required' => 'O campo Valor é obrigatório',
            'value.regex' => 'O campo Valor deve ser válido para depósito.',
            'payee_id.required' => 'Você deve informar para quem está transferindo.'
        ];
    }
}
