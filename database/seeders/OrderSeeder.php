<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

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
        $users = User::take(10)->get();
        if ($users->count() < 10) {
            $missing = 10 - $users->count();
            $users = $users->concat(User::factory()->count($missing)->create());
        }

        $products = Product::all();
        if ($products->isEmpty()) return; // nothing to seed

        $start = Carbon::create(2025, 6, 1, 0, 0, 0);
        $end = Carbon::create(2025, 9, 30, 23, 59, 59);

        $statuses = ['pending','processing','shipped','completed','cancelled'];
        $paymentMethods = ['credit_card','bank_transfer','ewallet'];
        $paymentStatuses = ['pending','processing','paid'];

        // Create 50 orders in the given range
        for ($i=0; $i<50; $i++) {
            $user = $users->random();
            $created = Carbon::createFromTimestamp(rand($start->timestamp, $end->timestamp));
            $status = $statuses[array_rand($statuses)];
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => 0,
                'status' => $status,
                'shipping_address' => $user->name."\nJl. Mawar No. ".rand(1,200).', Jakarta',
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'payment_status' => $paymentStatus,
                'payment_verified_at' => $paymentStatus === 'paid' ? $created : null,
                'created_at' => $created,
                'updated_at' => $created,
            ]);

            // 1-4 items per order
            $itemsCount = rand(1,4);
            $picked = $products->random($itemsCount);
            $total = 0;
            foreach ($picked as $p) {
                $qty = rand(1,3);
                $line = $p->price * $qty;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $p->id,
                    'quantity' => $qty,
                    'price' => $p->price,
                    'created_at' => $created,
                    'updated_at' => $created,
                ]);
                $total += $line;
            }
            $order->update(['total_amount' => $total]);
        }
    }
}
