<?php

namespace App\Models\Services;

use App\Models\Repositories\{UserRepository, WalletRepository};
use App\Exceptions\BusinessException;
use App\Models\Enums\UserType;

class UserService
{
    public function __construct(UserRepository $userRepository, WalletRepository $walletRepository)
    {
        $this->userRepository = $userRepository;
        $this->walletRepository = $walletRepository;
    }

    public function store($data)
    {
        $cpf_cnpj = $data['cpf_cnpj'];
        $email = $data['email'];

        $user_found = $this->userRepository->findExistence($cpf_cnpj, $email);

        if ($user_found) {
            throw new BusinessException('Usuário com CPF/CNPJ ou E-mail informados já cadastrado.', 409);
        }

        $user = $this->userRepository->store($data);
        
        $this->walletRepository->store(['balance' => 0.00, 'user_id' => $user->id]);

        return $user;
    }   

    public function deposit($id, $value)
    {
        $user_found = $this->userRepository->findBy('id', $id);

        if (!$user_found) {
            throw new BusinessException('Usuário informado não encontrado.', 404);
        }
        
        $this->walletRepository->deposit($user_found->wallet->id, $value);

        return 'Depósito realizado com sucesso';
    }

    public function transfer($payer_id, $payee_id, $value)
    {
        if ($payer_id == $payee_id) {
            throw new BusinessException('Não é possível realizar uma transferência para você mesmo.', 406);
        }

        $payer = $this->userRepository->findBy('id', $payer_id);
        $payee = $this->userRepository->findBy('id', $payee_id);
        
        if (!$payer || !$payee) {
            throw new BusinessException('Usuário informado não encontrado.', 404);
        }

        if ($payer->type != UserType::COMUM) {
            throw new BusinessException('Apenas usuários comuns podem realizar transferências.', 406);
        }

        if ($this->consultBalance($payer_id) < $value) {
            throw new BusinessException('Saldo insuficiente para realizar transferência.', 406);
        }

        $this->walletRepository->transfer($payer->wallet->id, $payee->wallet->id, $value);

        return 'Transferência realizada com sucesso';
    }

    public function consultBalance($id)
    {
        $user_found = $this->userRepository->findBy('id', $id);
        
        if (!$user_found) {
            throw new BusinessException('Usuário informado não encontrado.', 404);
        }

        return $user_found->wallet->balance;
    }
}




