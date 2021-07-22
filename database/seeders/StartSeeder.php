<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Enums\UserType;

class StartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Raiany',
                'email' => 'raiany@gmail.com',
                'password' => bcrypt('123123'),
                'cpf_cnpj' => '12312312387',
                'type' => UserType::COMUM
            ],
            [
                'name' => 'Thiago',
                'email' => 'thiago@gmail.com',
                'password' => bcrypt('123123'),
                'cpf_cnpj' => '12312312385',
                'type' => UserType::LOJISTA
            ],
            [
                'name' => 'Fabiana',
                'email' => 'fabiana@gmail.com',
                'password' => bcrypt('123123'),
                'cpf_cnpj' => '12312312381',
                'type' => UserType::COMUM
            ],
            [
                'name' => 'Diego',
                'email' => 'diego@gmail.com',
                'password' => bcrypt('123123'),
                'cpf_cnpj' => '12312312382',
                'type' => UserType::LOJISTA
            ],
        ]);
        
        DB::table('wallets')->insert([
            ['user_id' => 1, 'balance' => 345.5],
            ['user_id' => 2, 'balance' => 100],
            ['user_id' => 3, 'balance' => 1254.5],
            ['user_id' => 4, 'balance' => 7500.34],
        ]);
    }
}