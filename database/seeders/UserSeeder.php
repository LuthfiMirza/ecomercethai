<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profiles = [
            [
                'name' => 'Arthit Prasert',
                'email' => 'arthit@example.com',
            ],
            [
                'name' => 'Nalinee Suksawat',
                'email' => 'nalinee@example.com',
            ],
            [
                'name' => 'Kittikun Worraphan',
                'email' => 'kittikun@example.com',
            ],
            [
                'name' => 'Sunee Boonmee',
                'email' => 'sunee@example.com',
            ],
            [
                'name' => 'Wichai Rattanakorn',
                'email' => 'wichai@example.com',
            ],
            [
                'name' => 'Customer Demo',
                'email' => 'customer@example.com',
            ],
        ];

        foreach ($profiles as $profile) {
            User::firstOrCreate(
                ['email' => $profile['email']],
                [
                    'name' => $profile['name'],
                    'password' => Hash::make('password'),
                    'is_admin' => false,
                    'is_banned' => false,
                    'email_verified_at' => now()->subDays(rand(1, 60)),
                    'remember_token' => Str::random(10),
                ]
            );
        }
    }
}

