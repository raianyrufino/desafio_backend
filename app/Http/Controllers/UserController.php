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
        $answer = $this->service->store($request->all());

        return response()->json($answer, 201);
    }  
    
    public function deposit($id, DepositRequest $request)
    {
        $answer = $this->service->deposit($id, $request->value);

        return response()->json($answer, 200);
    }  

    public function transfer($id, TransferRequest $request)
    {
        $answer = $this->service->transfer($id, $request->payee_id, $request->value);

        return response()->json($answer, 200);
    }  

    public function balance($id)
    {
        $answer = $this->service->balance($id);

        return response()->json($answer, 200);
    }  
}
