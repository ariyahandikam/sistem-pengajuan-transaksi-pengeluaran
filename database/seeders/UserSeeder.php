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
            ['name' => 'Staff',    'email' => 'staff@test.com',    'role' => 'staff'],
            ['name' => 'SPV',      'email' => 'spv@test.com',      'role' => 'spv'],
            ['name' => 'Manager',  'email' => 'manager@test.com',  'role' => 'manager'],
            ['name' => 'Direktur', 'email' => 'direktur@test.com', 'role' => 'direktur'],
            ['name' => 'Finance',  'email' => 'finance@test.com',  'role' => 'finance'],
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
