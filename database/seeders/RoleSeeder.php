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

        User::chunk(100, function ($users) use ($userRole, $adminRole) {
            foreach ($users as $user) {
                if (! $user->hasRole($userRole->name)) {
                    $user->assignRole($userRole);
                }

                if ($user->is_admin && ! $user->hasRole($adminRole->name)) {
                    $user->assignRole($adminRole);
                }
            }
        });
    }
}
