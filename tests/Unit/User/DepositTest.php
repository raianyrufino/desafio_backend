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

class DepositTest extends TestCase 
{

    public function testCreateDepositWithSucess() 
    {
        $id = 1;
        $value = 200;

        $wallet = new stdClass;
        $wallet->id = 1;

        $user = new stdClass;
        $user->id = 1;
        $user->wallet = $wallet;

        $this->instance(UserRepository::class, Mockery::mock(UserRepository::class, function(MockInterface $mock) use($id, $user) {
            $mock->shouldReceive('findBy')
                ->with('id', $id)
                ->once()
                ->andReturn($user);
        }));

        $this->instance(WalletRepository::class, Mockery::mock(WalletRepository::class, function(MockInterface $mock) use($user, $value) {
            $mock->shouldReceive('deposit')
                ->with($user->wallet->id, $value)
                ->once();
        }));

        $userService = app(UserService::class);
        $userService->deposit($id, $value);
    }

    public function testCreateDepositWithoutUser() 
    {
        $this->expectException(BusinessException::class);

        $id = 1;
        $value = 200;

        $this->instance(UserRepository::class, Mockery::mock(UserRepository::class, function(MockInterface $mock) use($id) {
            $mock->shouldReceive('findBy')
                ->with('id', $id)
                ->once()
                ->andReturn(null);
        }));

        $userService = app(UserService::class);
        $userService->deposit($id, $value);  
    }
}