<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;  
use App\Models\Services\UserService;
use App\Http\Requests\User\CreateRequest;

class UserController extends Controller
{
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function store(CreateRequest $request)
    {
        return $this->userService->store($request->all());
    }  
    
    public function deposit($id, Request $request)
    {
        return $this->userService->deposit($id, $request->value);
    }  

    public function transfer($id, Request $request)
    {
        return $this->userService->transfer($id, $request->payee_id, $request->value);
    }  

    public function consultBalance($id)
    {
        return $this->userService->consultBalance($id);
    }  
}
