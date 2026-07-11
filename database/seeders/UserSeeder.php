<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Staff Demo',    'email' => 'staff@company.com',    'role' => 'staff'],
            ['name' => 'SPV Demo',      'email' => 'spv@company.com',      'role' => 'spv'],
            ['name' => 'Manager Demo',  'email' => 'manager@company.com',  'role' => 'manager'],
            ['name' => 'Direktur Demo', 'email' => 'direktur@company.com', 'role' => 'direktur'],
            ['name' => 'Finance Demo',  'email' => 'finance@company.com',  'role' => 'finance'],
        ];

        foreach ($users as $userData) {
            $role = Role::where('slug', $userData['role'])->first();

            User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name'              => $userData['name'],
                    'password'          => Hash::make('password'),
                    'role_id'           => $role?->id,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
