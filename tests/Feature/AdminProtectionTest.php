<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard', ['locale' => 'en']));

        $response->assertRedirect(route('login', ['locale' => 'en']));
    }

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.dashboard', ['locale' => 'en']));

        $response->assertStatus(404);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create();
        $adminRole = Role::create(['name' => 'admin']);
        $admin->assignRole($adminRole);

        $response = $this->actingAs($admin)->get(route('admin.dashboard', ['locale' => 'en']));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_products_page(): void
    {
        $admin = User::factory()->create();
        $adminRole = Role::create(['name' => 'admin']);
        $admin->assignRole($adminRole);

        $response = $this->actingAs($admin)->get(route('admin.products.index', ['locale' => 'en']));

        $response->assertStatus(200);
    }

    public function test_regular_user_cannot_access_products_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.products.index', ['locale' => 'en']));

        $response->assertStatus(404);
    }
}
