<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $adminRoleId = Role::query()->firstOrCreate(['name' => 'admin'])->id;
        $technicianRoleId = Role::query()->firstOrCreate(['name' => 'technician'])->id;
        $clientRoleId = Role::query()->firstOrCreate(['name' => 'client'])->id;
        $cashierRoleId = Role::query()->firstOrCreate(['name' => 'cashier'])->id;

        User::updateOrCreate(['email' => 'admin@deskcir.com'], [
            'name' => 'Administrador',
            'email' => 'admin@deskcir.com',
            'password' => Hash::make('12345678'),
            'role_id' => $adminRoleId,
        ]);

        User::updateOrCreate(['email' => 'tec@deskcir.com'], [
            'name' => 'Tecnico Demo',
            'email' => 'tec@deskcir.com',
            'password' => Hash::make('12345678'),
            'role_id' => $technicianRoleId,
        ]);

        User::updateOrCreate(['email' => 'cliente@deskcir.com'], [
            'name' => 'Cliente Demo',
            'email' => 'cliente@deskcir.com',
            'password' => Hash::make('12345678'),
            'role_id' => $clientRoleId,
        ]);

        User::updateOrCreate(['email' => 'caja@deskcir.com'], [
            'name' => 'Caja Demo',
            'email' => 'caja@deskcir.com',
            'password' => Hash::make('12345678'),
            'role_id' => $cashierRoleId,
        ]);
    }
}
