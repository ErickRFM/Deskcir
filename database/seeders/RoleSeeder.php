<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        foreach (['admin', 'technician', 'client', 'cashier'] as $roleName) {
            Role::query()->firstOrCreate(['name' => $roleName]);
        }
    }
}
