<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        if ($admin = User::first()) {
            $admin->assignRole($userRole);
            if (! $admin->hasRole('admin')) {
                $admin->assignRole($adminRole);
            }
        }
    }
}
