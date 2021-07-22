<?php

namespace App\Models\Repositories;

abstract class BaseRepository
{
    protected $model;

    public function store($data)
    {
        return $this->model->create($data);   
    }

    public function findBy($field, $value)
    {   
        return $this->model->where($field, $value)->first();
    }
}