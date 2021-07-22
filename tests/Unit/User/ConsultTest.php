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

class ConsultTest extends TestCase 
{
    public function testConsultWithSucess() 
    {
        $id = 1;

        $this->instance(UserRepository::class, Mockery::mock(UserRepository::class, function(MockInterface $mock) use($id) {
            $wallet = new stdClass;
            $wallet->id = 1;
            $wallet->balance = 500;

            $user = new stdClass;
            $user->id = $id;
            $user->wallet = $wallet;

            $mock->shouldReceive('findBy')
                ->with('id', $id)
                ->once()
                ->andReturn($user);
        }));

        $userService = app(UserService::class);
        $userService->balance($id);
    }

    public function testCreateUserWithErro2r() 
    {
        $this->expectExceptionMessage('Usuário informado não encontrado.');
        
        $id = 1;

        $this->instance(UserRepository::class, Mockery::mock(UserRepository::class, function(MockInterface $mock) use($id) {
            $mock->shouldReceive('findBy')
                ->with('id', $id)
                ->once()
                ->andReturn(false);
        }));

        $userService = app(UserService::class);
        $userService->balance($id);

    }
}