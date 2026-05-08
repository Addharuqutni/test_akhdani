<?php

namespace Database\Seeders;

use App\Enums\RoleCode;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'code' => RoleCode::ADMIN->value],
            ['name' => 'Pegawai', 'code' => RoleCode::EMPLOYEE->value],
            ['name' => 'Divisi SDM', 'code' => RoleCode::HR->value],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(['code' => $role['code']], $role);
        }
    }
}
