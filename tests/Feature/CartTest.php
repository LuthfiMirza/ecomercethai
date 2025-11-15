<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_product_to_cart(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100, 'is_active' => true]);

        $response = $this->actingAs($user)->postJson(route('cart.add', ['locale' => 'en']), [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_user_can_add_product_with_color_option(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'price' => 150,
            'is_active' => true,
            'colors' => ['Black', 'White'],
        ]);

        $response = $this->actingAs($user)->postJson(route('cart.add', ['locale' => 'en']), [
            'product_id' => $product->id,
            'quantity' => 1,
            'color' => 'Black',
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'color' => 'Black',
        ]);
    }

    public function test_invalid_color_selection_is_rejected(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'price' => 200,
            'is_active' => true,
            'colors' => ['Red'],
        ]);

        $response = $this->actingAs($user)->postJson(route('cart.add', ['locale' => 'en']), [
            'product_id' => $product->id,
            'quantity' => 1,
            'color' => 'Blue',
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseMissing('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'color' => 'Blue',
        ]);
    }

    public function test_user_can_update_cart_quantity(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100, 'is_active' => true]);
        $cart = Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price,
        ]);

        $response = $this->actingAs($user)->putJson(route('cart.update', ['locale' => 'en', 'id' => $cart->id]), [
            'quantity' => 5,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'quantity' => 5,
        ]);
    }

    public function test_user_can_remove_product_from_cart(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100, 'is_active' => true]);
        $cart = Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price,
        ]);

        $response = $this->actingAs($user)->deleteJson(route('cart.remove', ['locale' => 'en', 'id' => $cart->id]));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('carts', [
            'id' => $cart->id,
        ]);
    }
}
