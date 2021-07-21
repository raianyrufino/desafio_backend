<?php

namespace App\Models\Repositories;

use App\Models\Entities\User;
use Illuminate\Database\Eloquent\Builder;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function findExistence($cpf_cnpj, $email)
    {   
        return $this->model->where(function (Builder $query) use($cpf_cnpj, $email) {
            return $query->where('cpf_cnpj', $cpf_cnpj)
                         ->orWhere('email', '>=', $email);
        })
        ->first();;
    }
}