<?php

namespace App\Models\Services;

use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    public function send($email)
    {   
        try {
            Http::get('http://o4d9z.mocklab.io/notify');
        } catch (\Exception $e) {
            throw new BusinessException('Ops, não foi possível enviar a notificação.', 503);
        }
    }
}