<?php

namespace App\Models\Services;

use App\Models\Repositories\{UserRepository, WalletRepository};
use App\Exceptions\BusinessException;
use App\Models\Enums\UserType;
use DB;
use Illuminate\Support\Facades\Http;

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
            $data['password'] = bcrypt($data['password']);

            $user = $this->repository->store($data);
                
            $wallet = [
                'balance' => 0.00, 
                'user_id' => $user->id
            ];

            $this->walletRepository->store($wallet);
            
            DB::commit();

            return response()->json('Usuário criado com sucesso', 201);
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

        $response = Http::get('https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6')->object();

        if ($response->message != 'Autorizado') {
            throw new BusinessException('Você não possui autorização para realizar transferência.', 401);
        }

        DB::beginTransaction();

        try {
            $this->walletRepository->transfer($payer->wallet->id, $payee->wallet->id, $value);
            
            $this->sendNotification($payee->email);
            
            DB::commit();

            return response()->json('Transferência realizada com sucesso.', 200);
        } catch(\Exception $e){
            DB::rollback();
            
            $codigo = $e->getCode();
            $resposta = ['erro' => $e->getMessage()];

            return response()->json($resposta, $codigo);
        }
    }

    private function sendNotification($email)
    {   
        try {
            Http::get('http://o4d9z.mocklab.io/notify', [
                'email' => $email,
            ]);
        } catch (\Exception $e) {
            throw new BusinessException('Ops, não foi possível enviar a notificação.', 503);
        }
    }

    public function consultBalance($id)
    {
        $user = $this->repository->findBy('id', $id);
        
        if (!$user) {
            throw new BusinessException('Usuário informado não encontrado.', 404);
        }

        return response()->json($user->wallet->balance, 200);
    }
}