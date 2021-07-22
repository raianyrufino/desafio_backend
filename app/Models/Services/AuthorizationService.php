<?php

namespace App\Models\Services;

use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\Http;

class AuthorizationService
{
    public function get()
    {
        return Http::get('https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6')->object()->message;
    }
}