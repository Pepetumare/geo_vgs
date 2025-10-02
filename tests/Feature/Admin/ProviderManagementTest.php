<?php

namespace Tests\Feature\Admin;

use App\Models\Provider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_provider(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.providers.store'), [
            'name' => 'Proveedor Uno',
            'contact_name' => 'Juan Perez',
            'email' => 'contacto@example.com',
            'phone' => '987654321',
            'address' => 'Av. Principal 123',
            'notes' => 'Proveedor de papelería',
        ]);

        $provider = Provider::first();

        $response->assertRedirect(route('admin.providers.show', $provider));

        $this->assertDatabaseHas('providers', [
            'name' => 'Proveedor Uno',
            'email' => 'contacto@example.com',
        ]);
    }

    public function test_admin_can_add_supply_to_provider(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $provider = Provider::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.providers.supplies.store', $provider), [
            'name' => 'Resmas de papel',
            'description' => 'Resma tamaño A4',
            'unit' => 'Paquete',
            'unit_price' => 25.5,
            'stock' => 10,
        ]);

        $response->assertRedirect(route('admin.providers.show', $provider));

        $this->assertDatabaseHas('supplies', [
            'provider_id' => $provider->id,
            'name' => 'Resmas de papel',
            'stock' => 10,
        ]);
    }

    public function test_admin_can_search_supplies_by_name(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $provider = Provider::factory()->create(['name' => 'Proveedor Lima']);
        $provider->supplies()->create([
            'name' => 'Resma tamaño oficio',
            'description' => '500 hojas color blanco',
            'unit' => 'Paquete',
            'unit_price' => 30.5,
            'stock' => 15,
        ]);

        $provider->supplies()->create([
            'name' => 'Tinta para impresora',
            'description' => 'Cartucho negro',
            'unit' => 'Unidad',
            'unit_price' => 80,
            'stock' => 4,
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.supplies.search', ['q' => 'resma']));

        $response->assertOk();

        $response->assertJson(fn ($json) => $json
            ->has('data', fn ($json) => $json
                ->first(fn ($json) => $json
                    ->where('name', 'Resma tamaño oficio')
                    ->where('provider.name', 'Proveedor Lima')
                    ->etc()
                )
            )
        );
    }
}
