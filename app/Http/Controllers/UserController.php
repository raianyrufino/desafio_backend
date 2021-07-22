<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;  
use App\Models\Services\UserService;
use App\Http\Requests\User\{CreateRequest, DepositRequest, TransferRequest};

class UserController extends Controller
{
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function store(CreateRequest $request)
    {
        return $this->service->store($request->all());
    }  
    
    public function deposit($id, DepositRequest $request)
    {
        return $this->service->deposit($id, $request->value);
    }  

    public function transfer($id, TransferRequest $request)
    {
        return $this->service->transfer($id, $request->payee_id, $request->value);
    }  

    public function consultBalance($id)
    {
        return $this->service->consultBalance($id);
    }  
}
