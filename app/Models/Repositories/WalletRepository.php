<?php

namespace App\Models\Repositories;

use App\Models\Entities\Wallet;

class WalletRepository extends BaseRepository
{
    public function __construct(Wallet $model)
    {
        $this->model = $model;
    }

    public function deposit($id, $value) 
    {
        $this->model->whereId($id)->increment('balance', $value);
    }

    public function transfer($payer_id, $payee_id, $value) 
    {
        $this->model->whereUserId($payer_id)->decrement('balance', $value);
        $this->model->whereUserId($payee_id)->increment('balance', $value);
    }
}