<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'is_banned' => false,
            ]
        );

        $this->call([
            UserSeeder::class,
            RoleSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            CouponSeeder::class,
            BannerSeeder::class,
            MegaMenuSeeder::class,
            CartSeeder::class,
            OrderSeeder::class,
        ]);

        if (env('SEED_DEMO_DATA', false)) {
            $this->call([
                ShippingAddressSeeder::class,
                WishlistSeeder::class,
            ]);
        }

        if (method_exists($user, 'assignRole')) {
            $user->assignRole('admin');
        }
    }
}
