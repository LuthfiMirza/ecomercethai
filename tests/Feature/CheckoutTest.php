<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ShippingAddress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_apply_valid_coupon(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 1000, 'is_active' => true]);
        
        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price,
        ]);

        $coupon = Coupon::create([
            'code' => 'TEST10',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'min_purchase' => 500,
            'is_active' => true,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addDay(),
        ]);

        $response = $this->actingAs($user)->postJson('/checkout/apply-coupon', [
            'coupon_code' => 'TEST10',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'discount_amount' => 100,
            ]);
    }

    public function test_user_cannot_apply_invalid_coupon(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 1000, 'is_active' => true]);
        
        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price,
        ]);

        $response = $this->actingAs($user)->postJson('/checkout/apply-coupon', [
            'coupon_code' => 'INVALID',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => false]);
    }

    public function test_user_can_checkout_with_valid_data(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 1000, 'is_active' => true]);
        
        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price,
        ]);

        $address = ShippingAddress::create([
            'user_id' => $user->id,
            'name' => 'John Doe',
            'phone' => '1234567890',
            'address_line1' => '123 Main St',
            'city' => 'Bangkok',
            'state' => 'Bangkok',
            'postal_code' => '10100',
            'country' => 'Thailand',
        ]);

        $response = $this->actingAs($user)->post('/checkout', [
            'shipping_address_id' => $address->id,
            'payment_method' => 'bank_transfer',
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'payment_method' => 'bank_transfer',
        ]);
    }
}
