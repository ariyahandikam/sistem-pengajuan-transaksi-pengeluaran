<?php

namespace Database\Seeders;

use App\Models\Role as AppRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role as SpatieRole;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'slug' => 'admin'],
            ['name' => 'Staff', 'slug' => 'staff'],
            ['name' => 'SPV', 'slug' => 'spv'],
            ['name' => 'Manager', 'slug' => 'manager'],
            ['name' => 'Direktur', 'slug' => 'direktur'],
            ['name' => 'Finance', 'slug' => 'finance'],
        ];

        foreach ($roles as $r) {
            AppRole::firstOrCreate(['slug' => $r['slug']], ['name' => $r['name']]);
        }

        // Create admin user if not exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'status' => 'active',
            ]
        );

        $adminRole = AppRole::where('slug', 'admin')->first();
        if ($adminRole) {
            $admin->role()->associate($adminRole);
            $admin->save();
        }

        if (method_exists($admin, 'assignRole')) {
            $admin->assignRole('Admin');
        }
    }
}
