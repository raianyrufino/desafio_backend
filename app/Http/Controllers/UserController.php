<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;  
use App\Models\Services\UserService;
use App\Http\Requests\User\CreateRequest;

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
    
    public function deposit($id, Request $request)
    {
        return $this->service->deposit($id, $request->value);
    }  

    public function transfer($id, Request $request)
    {
        return $this->service->transfer($id, $request->payee_id, $request->value);
    }  

    public function consultBalance($id)
    {
        $answer = $this->service->consultBalance($id);

        return response()->json($answer, 200);
    }  
}
