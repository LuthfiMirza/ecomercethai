<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Coupon;
use App\Models\ShippingAddress;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clean existing orders and items
        Schema::disableForeignKeyConstraints();
        OrderItem::query()->delete();
        Order::query()->delete();
        Schema::enableForeignKeyConstraints();

        // Ensure we have some users
        $users = User::all();
        if ($users->isEmpty()) {
            $users = User::factory()->count(5)->create();
        }

        $products = Product::all();
        if ($products->isEmpty()) {
            return; // nothing to seed
        }

        $coupons = Coupon::where('is_active', true)->get();

        $start = Carbon::create(2025, 6, 1, 0, 0, 0);
        $end = Carbon::create(2025, 9, 30, 23, 59, 59);

        $statuses = ['pending','processing','shipped','completed','cancelled'];
        $paymentMethods = ['bank_transfer'];
        $paymentStatuses = ['pending','processing','paid'];
        $shippingFees = [50, 75, 99, 120];

        // Create 50 orders in the given range
        for ($i = 0; $i < 50; $i++) {
            $user = $users->random();
            $created = Carbon::createFromTimestamp(rand($start->timestamp, $end->timestamp));
            $status = $statuses[array_rand($statuses)];
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            $shippingFee = $shippingFees[array_rand($shippingFees)];

            $address = ShippingAddress::where('user_id', $user->id)->inRandomOrder()->first();
            $shippingPayload = $address ? [
                'name' => $address->name,
                'phone' => $address->phone,
                'address_line1' => $address->address_line1,
                'address_line2' => $address->address_line2,
                'city' => $address->city,
                'state' => $address->state,
                'postal_code' => $address->postal_code,
                'country' => $address->country,
            ] : [
                'name' => $user->name,
                'phone' => '0800-000-000',
                'address_line1' => '123 Demo Street',
                'address_line2' => 'Unit '.rand(1, 30),
                'city' => 'Bangkok',
                'state' => 'Bangkok',
                'postal_code' => '10110',
                'country' => 'Thailand',
            ];

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => 0,
                'status' => $status,
                'shipping_address' => json_encode($shippingPayload),
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'payment_verified_at' => $paymentStatus === 'paid' ? $created->copy()->addHours(rand(2, 24)) : null,
                'coupon_code' => null,
                'discount_amount' => 0,
                'created_at' => $created,
                'updated_at' => $created,
            ]);

            // 1-4 items per order
            $itemsCount = rand(1, 4);
            $picked = $products->random(min($itemsCount, $products->count()));
            $subtotal = 0;

            foreach ($picked as $product) {
                $qty = rand(1, 3);
                $line = $product->price * $qty;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $product->price,
                    'created_at' => $created,
                    'updated_at' => $created,
                ]);

                $subtotal += $line;
            }

            $couponCode = null;
            $discountAmount = 0;
            if ($subtotal > 0 && $coupons->isNotEmpty() && rand(0, 1) === 1) {
                $coupon = $coupons->random();
                if ($subtotal >= $coupon->min_purchase) {
                    if ($coupon->discount_type === 'percentage') {
                        $discountAmount = ($subtotal * $coupon->discount_value) / 100;
                        if ($coupon->max_discount) {
                            $discountAmount = min($discountAmount, $coupon->max_discount);
                        }
                    } else {
                        $discountAmount = $coupon->discount_value;
                    }
                    $couponCode = $coupon->code;
                }
            }

            $totalAmount = max($subtotal - $discountAmount + $shippingFee, 0);

            $order->update([
                'total_amount' => $totalAmount,
                'coupon_code' => $couponCode,
                'discount_amount' => $discountAmount,
            ]);
        }
    }
}
