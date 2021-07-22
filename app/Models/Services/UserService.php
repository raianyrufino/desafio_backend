<?php

namespace App\Models\Services;

use App\Models\Repositories\{UserRepository, WalletRepository};
use App\Exceptions\BusinessException;
use App\Models\Enums\UserType;
use DB;

class UserService
{
    public function __construct(UserRepository $repository, WalletRepository $walletRepository)
    {
        $this->repository = $repository;
        $this->walletRepository = $walletRepository;
    }

    public function store($data)
    {
        DB::beginTransaction();

        try {
            $user = $this->repository->store($data);
                
            $wallet = [
                'balance' => 0.00, 
                'user_id' => $user->id
            ];

            $this->walletRepository->store($wallet);
            
            DB::commit();

            return response()->json('Usuário criado com sucesso', 200);
        } catch(\Exception $e){
            DB::rollback();
            
            $codigo = $e->getCode();
            $resposta = ['erro' => $e->getMessage()];

            return response()->json($resposta, $codigo);
        }
    }   

    public function deposit($id, $value)
    {
        DB::beginTransaction();

        try {
            $user = $this->repository->findBy('id', $id);

            if (!$user) {
                throw new BusinessException('Usuário informado não encontrado.', 404);
            }

            $this->walletRepository->deposit($user->wallet->id, $value);
            
            DB::commit();

            return response()->json('Depósito realizado com sucesso.', 200);
        } catch(\Exception $e){
            DB::rollback();
            
            $codigo = $e->getCode();
            $resposta = ['erro' => $e->getMessage()];

            return response()->json($resposta, $codigo);
        }    
    }

    public function transfer($payer_id, $payee_id, $value)
    {
        if ($payer_id == $payee_id) {
            throw new BusinessException('Não é possível realizar uma transferência para você mesmo.', 406);
        }

        $payer = $this->repository->findBy('id', $payer_id);
        $payee = $this->repository->findBy('id', $payee_id);
        
        if (!$payer || !$payee) {
            throw new BusinessException('Usuário informado não encontrado.', 404);
        }

        if ($payer->type != UserType::COMUM) {
            throw new BusinessException('Apenas usuários comuns podem realizar transferências.', 406);
        }

        if ($this->consultBalance($payer_id) < $value) {
            throw new BusinessException('Saldo insuficiente para realizar transferência.', 406);
        }

        DB::beginTransaction();

        try {
            $this->walletRepository->transfer($payer->wallet->id, $payee->wallet->id, $value);
            
            DB::commit();

            return response()->json('Transferência realizada com sucesso.', 200);
        } catch(\Exception $e){
            DB::rollback();
            
            $codigo = $e->getCode();
            $resposta = ['erro' => $e->getMessage()];

            return response()->json($resposta, $codigo);
        }
    }

    public function consultBalance($id)
    {
        $user = $this->repository->findBy('id', $id);
        
        if (!$user) {
            throw new BusinessException('Usuário informado não encontrado.', 404);
        }

        return $user->wallet->balance;
    }
}