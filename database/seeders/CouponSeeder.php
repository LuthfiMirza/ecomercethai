<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Coupon::query()->delete();
        Schema::enableForeignKeyConstraints();

        $today = now();

        $coupons = [
            [
                'code' => 'WELCOME10',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'min_purchase' => 1000,
                'max_discount' => 500,
                'is_active' => true,
                'valid_from' => $today->clone()->subMonth(),
                'valid_until' => $today->clone()->addMonths(3),
                'usage_limit' => 500,
                'used_count' => 0,
            ],
            [
                'code' => 'FREESHIP',
                'discount_type' => 'fixed',
                'discount_value' => 150,
                'min_purchase' => 800,
                'max_discount' => null,
                'is_active' => true,
                'valid_from' => $today->clone()->subWeeks(2),
                'valid_until' => $today->clone()->addMonths(2),
                'usage_limit' => 300,
                'used_count' => 0,
            ],
            [
                'code' => 'MIDNIGHT20',
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'min_purchase' => 2500,
                'max_discount' => 800,
                'is_active' => true,
                'valid_from' => $today->clone()->subDays(7),
                'valid_until' => $today->clone()->addDays(21),
                'usage_limit' => 200,
                'used_count' => 0,
            ],
        ];

        foreach ($coupons as $coupon) {
            $coupon['code'] = Str::upper($coupon['code']);
            Coupon::create($coupon);
        }
    }
}

