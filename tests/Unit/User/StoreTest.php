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

class StoreTest extends TestCase 
{

    public function testCreateUserWithSucess() 
    {
        $request = new Request([
            "name" => "Ana",
            "password" => "123123",
            "email" => "ana@gmail.com",
            "cpf_cnpj" => "12312312387",
            "type" => "COMUM"
        ]);

        $this->instance(UserRepository::class, Mockery::mock(UserRepository::class, function(MockInterface $mock) use($request) {
            $user = new stdClass;
            $user->id = 1;

            $mock->shouldReceive('store')
                ->with($request)
                ->once()
                ->andReturn($user);
        }));

        $this->instance(WalletRepository::class, Mockery::mock(WalletRepository::class, function(MockInterface $mock) use($request) {
            $mock->shouldReceive('store')
                ->once();
        }));

        $userService = app(UserService::class);
        $userService->store($request);
    }

    public function testCreateUserWithError() 
    {

        $this->expectException(BusinessException::class);
        $request = new Request([
            "name" => "Ana",
            "password" => "123123",
            "email" => "ana@gmail.com",
            "cpf_cnpj" => "12312312387",
            "type" => "COMUM"
        ]);

        $this->instance(UserRepository::class, Mockery::mock(UserRepository::class, function(MockInterface $mock) use($request) {
            $user = new stdClass;

            $mock->shouldReceive('store')
                ->with($request)
                ->once()
                ->andReturn($user);
        }));

        $userService = app(UserService::class);
        $userService->store($request);

    }
}