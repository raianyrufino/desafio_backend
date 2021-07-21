<?php

namespace App\Models\Repositories;

use App\Models\Entities\Wallet;
use DB;

class WalletRepository extends BaseRepository
{
    public function __construct(Wallet $model)
    {
        $this->model = $model;
    }

    public function deposit($id, $value) 
    {
        DB::transaction(function () use($id, $value) {   
            $this->model->whereId($id)->increment('balance', $value);
        });
    }

    public function transfer($payer_wallet_id, $payee_wallet_id, $value) 
    {
        DB::transaction(function () use($payer_wallet_id, $payee_wallet_id, $value) {   
            $this->model->whereId($payer_wallet_id)->decrement('balance', $value);
            $this->model->whereId($payee_wallet_id)->increment('balance', $value);
        });
    }
}