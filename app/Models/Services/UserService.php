<?php

namespace App\Models\Services;

use App\Models\Repositories\{UserRepository, WalletRepository};
use App\Models\Services\{NotificationService, AuthorizationService};
use App\Exceptions\BusinessException;
use App\Models\Enums\UserType;
use DB;

class UserService
{
    public function __construct(
        UserRepository $repository, 
        WalletRepository $walletRepository, 
        NotificationService $notificationService, 
        AuthorizationService $authorizationService
    ) {
        $this->repository = $repository;
        $this->walletRepository = $walletRepository;
        $this->notificationService = $notificationService;
        $this->authorizationService = $authorizationService;
    }

    public function store($data)
    {
        DB::beginTransaction();

        try {
            $data['password'] = bcrypt($data['password']);

            $user = $this->repository->store($data);
            
            $wallet = [
                'balance' => 0.00, 
                'user_id' => $user->id
            ];

            $this->walletRepository->store($wallet);
            
            DB::commit();

            return 'Usuário criado com sucesso';
        } catch(\Exception $e){
            DB::rollback();
            
            throw new BusinessException($e->getMessage(), $e->getCode());
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

            return 'Depósito realizado com sucesso.';
        } catch(\Exception $e){
            DB::rollback();
            
            throw new BusinessException($e->getMessage(), $e->getCode());
        }    
    }

    public function transfer($payer_id, $payee_id, $value)
    {
        if ($payer_id == $payee_id) {
            throw new BusinessException('Não é possível realizar uma transferência para você mesmo.', 422);
        }
        
        $payer = $this->repository->findBy('id', $payer_id);
        $payee = $this->repository->findBy('id', $payee_id);

        if (!$payer || !$payee) {
            throw new BusinessException('Usuário informado não encontrado.', 404);
        }

        if ($payer->type != UserType::COMUM) {
            throw new BusinessException('Apenas usuários comuns podem realizar transferências.', 403);
        }

        if ($payer->wallet->balance < $value) {
            throw new BusinessException('Saldo insuficiente para realizar transferência.', 422);
        }

        $autorization_message = $this->authorizationService->get();
        
        if ($autorization_message != 'Autorizado') {
            throw new BusinessException('Você não possui autorização para realizar transferência.', 401);
        }

        DB::beginTransaction();
        try {
            $this->walletRepository->transfer($payer->wallet->id, $payee->wallet->id, $value);
            
            $this->notificationService->send($payee->email);
            
            DB::commit();

            return 'Transferência realizada com sucesso.';
        } catch(\Exception $e){
            DB::rollback();
            
            throw new BusinessException($e->getMessage(), $e->getCode());
        }
    }

    public function balance($id)
    {
        $user = $this->repository->findBy('id', $id);
        
        if (!$user) {
            throw new BusinessException('Usuário informado não encontrado.', 404);
        }

        return $user->wallet->balance;
    }
}