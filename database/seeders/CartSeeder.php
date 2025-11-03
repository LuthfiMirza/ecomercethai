<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cart::query()->delete();

        $customer = User::where('email', 'customer@example.com')->first()
            ?? User::where('is_admin', false)->first();

        if (! $customer) {
            return;
        }

        $products = Product::inRandomOrder()->take(4)->get();
        if ($products->isEmpty()) {
            return;
        }

        foreach ($products->take(2) as $product) {
            Cart::create([
                'user_id' => $customer->id,
                'product_id' => $product->id,
                'quantity' => rand(1, 3),
                'price' => $product->price,
            ]);
        }
    }
}

