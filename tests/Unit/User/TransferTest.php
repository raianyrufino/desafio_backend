<?php

namespace Testes\Unit\User;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use stdClass;
use App\Models\Repositories\{UserRepository, WalletRepository};
use Illuminate\Http\Request;  
use App\Models\Services\UserService;
use App\Exceptions\BusinessException;
use App\Models\Services\{NotificationService, AuthorizationService};
use App\Models\Enums\UserType;

class TransferTest extends TestCase 
{

    public function testTransferWithSucess() 
    {
        $payer_id = 1;
        $payee_id = 2;
        $value = 200;

        $wallet = new stdClass;
        $wallet->id = 1;
        $wallet->balance = 500;

        $payer = new stdClass;
        $payer->id = $payer_id;
        $payer->wallet = $wallet;
        $payer->type = UserType::COMUM;

        $payee = new stdClass;
        $payee->id = $payee_id;
        $payee->email = "payee@teste.com";
        $payee->wallet = $wallet;

        $this->instance(UserRepository::class, Mockery::mock(UserRepository::class, function(MockInterface $mock) use($payer_id, $payee_id, $payer, $payee) {
            $mock->shouldReceive('findBy')
                ->with('id', $payer_id)
                ->once()
                ->andReturn($payer);

            $mock->shouldReceive('findBy')
                ->with('id', $payee_id)
                ->once()
                ->andReturn($payee);
        }));

        $this->instance(AuthorizationService::class, Mockery::mock(AuthorizationService::class, function(MockInterface $mock) use($payer, $payee, $value) {
            $mock->shouldReceive('get')
                ->once()
                ->andReturn('Autorizado');
        }));

        $this->instance(WalletRepository::class, Mockery::mock(WalletRepository::class, function(MockInterface $mock) use($payer, $payee, $value) {
            $mock->shouldReceive('transfer')
                ->with($payer->wallet->id, $payee->wallet->id, $value)
                ->once();
        }));

        $this->instance(NotificationService::class, Mockery::mock(NotificationService::class, function(MockInterface $mock) use($payee) {
            $mock->shouldReceive('send')
                ->with($payee->email)
                ->once();
        }));

        $userService = app(UserService::class);
        $userService->transfer($payer_id, $payee_id, $value);
    }

    public function testCreateTransferWithSameUser() 
    {
        $this->expectExceptionMessage('Não é possível realizar uma transferência para você mesmo.');

        $payer_id = 1;
        $payee_id = 1;
        $value = 200;

        $wallet = new stdClass;
        $wallet->id = 1;
        $wallet->balance = 500;

        $payer = new stdClass;
        $payer->id = $payer_id;
        $payer->wallet = $wallet;
        $payer->type = UserType::COMUM;

        $payee = new stdClass;
        $payee->id = $payee_id;
        $payee->email = "payee@teste.com";
        $payee->wallet = $wallet;

        
        $userService = app(UserService::class);
        $userService->transfer($payer_id, $payee_id, $value);
    }

    public function testTransferWithoutUser() 
    {
        $this->expectExceptionMessage('Usuário informado não encontrado.');

        $payer_id = 1;
        $payee_id = 2;
        $value = 200;

        $wallet = new stdClass;
        $wallet->id = 1;
        $wallet->balance = 500;

        $payer = new stdClass;
        $payer->id = $payer_id;
        $payer->wallet = $wallet;
        $payer->type = UserType::COMUM;

        $payee = new stdClass;
        $payee->id = $payee_id;
        $payee->email = "payee@teste.com";
        $payee->wallet = $wallet;

        $this->instance(UserRepository::class, Mockery::mock(UserRepository::class, function(MockInterface $mock) use($payer_id, $payee_id, $payer, $payee) {
            $mock->shouldReceive('findBy')
                ->with('id', $payer_id)
                ->once()
                ->andReturn(null);

            $mock->shouldReceive('findBy')
                ->with('id', $payee_id)
                ->once()
                ->andReturn(null);
        }));

        $userService = app(UserService::class);
        $userService->transfer($payer_id, $payee_id, $value);
    }

    public function testTransferWithUserNotComum() 
    {
        $this->expectExceptionMessage('Apenas usuários comuns podem realizar transferências.');

        $payer_id = 1;
        $payee_id = 2;
        $value = 200;

        $wallet = new stdClass;
        $wallet->id = 1;
        $wallet->balance = 500;

        $payer = new stdClass;
        $payer->id = $payer_id;
        $payer->wallet = $wallet;
        $payer->type = UserType::LOJISTA;

        $payee = new stdClass;
        $payee->id = $payee_id;
        $payee->email = "payee@teste.com";
        $payee->wallet = $wallet;

        $this->instance(UserRepository::class, Mockery::mock(UserRepository::class, function(MockInterface $mock) use($payer_id, $payee_id, $payer, $payee) {
            $mock->shouldReceive('findBy')
                ->with('id', $payer_id)
                ->once()
                ->andReturn($payer);

            $mock->shouldReceive('findBy')
                ->with('id', $payee_id)
                ->once()
                ->andReturn($payee);
        }));

        $userService = app(UserService::class);
        $userService->transfer($payer_id, $payee_id, $value);
    }

    public function testTransferWithoutBalance() 
    {
        $this->expectExceptionMessage('Saldo insuficiente para realizar transferência.');

        $payer_id = 1;
        $payee_id = 2;
        $value = 600;

        $wallet = new stdClass;
        $wallet->id = 1;
        $wallet->balance = 500;

        $payer = new stdClass;
        $payer->id = $payer_id;
        $payer->wallet = $wallet;
        $payer->type = UserType::COMUM;

        $payee = new stdClass;
        $payee->id = $payee_id;
        $payee->email = "payee@teste.com";
        $payee->wallet = $wallet;

        $this->instance(UserRepository::class, Mockery::mock(UserRepository::class, function(MockInterface $mock) use($payer_id, $payee_id, $payer, $payee) {
            $mock->shouldReceive('findBy')
                ->with('id', $payer_id)
                ->once()
                ->andReturn($payer);

            $mock->shouldReceive('findBy')
                ->with('id', $payee_id)
                ->once()
                ->andReturn($payee);
        }));

        $userService = app(UserService::class);
        $userService->transfer($payer_id, $payee_id, $value);
    }

    public function testTransferWithAutorization() 
    {
        $this->expectExceptionMessage('Você não possui autorização para realizar transferência.');

        $payer_id = 1;
        $payee_id = 2;
        $value = 200;

        $wallet = new stdClass;
        $wallet->id = 1;
        $wallet->balance = 500;

        $payer = new stdClass;
        $payer->id = $payer_id;
        $payer->wallet = $wallet;
        $payer->type = UserType::COMUM;

        $payee = new stdClass;
        $payee->id = $payee_id;
        $payee->email = "payee@teste.com";
        $payee->wallet = $wallet;

        $this->instance(UserRepository::class, Mockery::mock(UserRepository::class, function(MockInterface $mock) use($payer_id, $payee_id, $payer, $payee) {
            $mock->shouldReceive('findBy')
                ->with('id', $payer_id)
                ->once()
                ->andReturn($payer);

            $mock->shouldReceive('findBy')
                ->with('id', $payee_id)
                ->once()
                ->andReturn($payee);
        }));

        $this->instance(AuthorizationService::class, Mockery::mock(AuthorizationService::class, function(MockInterface $mock) use($payer, $payee, $value) {
            $mock->shouldReceive('get')
                ->once()
                ->andReturn('Negado');
        }));

        $userService = app(UserService::class);
        $userService->transfer($payer_id, $payee_id, $value);
    }
}