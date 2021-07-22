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
            'value' => 'required',
            'payee_id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'value.required' => 'O campo Valor é obrigatório',
            'payee_id.required' => 'Você deve informar para quem está transferindo.'
        ];
    }
}
