<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {

        User::create([
            'name' => 'Administrador',
            'email' => 'admin@deskcir.com',
            'password' => Hash::make('12345678'),
            'role_id' => 1
        ]);

        User::create([
            'name' => 'Tecnico Demo',
            'email' => 'tec@deskcir.com',
            'password' => Hash::make('12345678'),
            'role_id' => 2
        ]);

        User::create([
            'name' => 'Cliente Demo',
            'email' => 'cliente@deskcir.com',
            'password' => Hash::make('12345678'),
            'role_id' => 3
        ]);
    }
}