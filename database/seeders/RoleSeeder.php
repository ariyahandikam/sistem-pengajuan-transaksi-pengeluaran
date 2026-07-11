<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Staff',    'slug' => 'staff'],
            ['name' => 'Supervisor', 'slug' => 'spv'],
            ['name' => 'Manager',  'slug' => 'manager'],
            ['name' => 'Direktur', 'slug' => 'direktur'],
            ['name' => 'Finance',  'slug' => 'finance'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
