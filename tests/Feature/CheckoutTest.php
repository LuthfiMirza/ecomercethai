<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingAddress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('en');
    }

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

        $response = $this->actingAs($user)->postJson(route('checkout.apply-coupon', ['locale' => 'en']), [
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

        $response = $this->actingAs($user)->postJson(route('checkout.apply-coupon', ['locale' => 'en']), [
            'coupon_code' => 'INVALID',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => false]);
    }

    public function test_user_can_checkout_with_valid_data(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 1000, 'is_active' => true]);

        Storage::fake('public');
        
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

        $proof = UploadedFile::fake()->image('proof.jpg');

        $response = $this->actingAs($user)->post(route('checkout.process', ['locale' => 'en']), [
            'shipping_address_id' => $address->id,
            'payment_method' => 'bank_transfer',
            'payment_proof' => $proof,
        ]);

        $order = Order::first();
        $this->assertNotNull($order);

        $response->assertRedirect(route('orders.show', ['locale' => app()->getLocale(), 'order' => $order]));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $user->id,
            'payment_method' => 'bank_transfer',
            'payment_status' => 'verifying',
        ]);

        Storage::disk('public')->assertExists('payment-proofs/'.$proof->hashName());
    }

    public function test_order_detail_route_returns_success_for_owner(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 1500, 'is_active' => true]);

        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 1550,
            'status' => 'pending',
            'payment_status' => 'pending',
            'shipping_address' => json_encode([
                'name' => 'Tester',
                'phone' => '0800000000',
                'address_line1' => '123 Test Street',
                'address_line2' => null,
                'city' => 'Bangkok',
                'state' => 'Bangkok',
                'postal_code' => '10110',
                'country' => 'Thailand',
            ]),
            'payment_method' => 'bank_transfer',
        ]);

        $order->orderItems()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price,
        ]);

        $response = $this->actingAs($user)->get(route('orders.show', [
            'locale' => 'en',
            'order' => $order->id,
        ]));

        $response->assertStatus(200)
            ->assertSee((string) $order->id)
            ->assertSee($product->name);
    }
}
