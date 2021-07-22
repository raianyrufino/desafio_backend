<?php

namespace App\Models\Repositories;

use App\Models\Entities\User;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }
}