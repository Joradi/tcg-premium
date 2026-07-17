<?php

use App\Livewire\Admin\OrderManager;
use App\Models\Order;
use App\Models\User;
use Livewire\Livewire;

it('trata los intentos de inyección SQL como texto de búsqueda', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    Order::create([
        'user_id' => null,
        'status' => 'pending',
        'customer_name' => 'Cliente legítimo',
        'customer_email' => 'legitimo@example.com',
        'customer_phone' => null,
        'shipping_address_line_1' => 'Avenida Segura 123',
        'shipping_address_line_2' => null,
        'shipping_city' => 'Santiago',
        'shipping_region' => 'Región Metropolitana',
        'shipping_postal_code' => null,
        'shipping_country' => 'Chile',
        'subtotal' => 11900,
        'tax_total' => 1900,
        'shipping_total' => 0,
        'total' => 11900,
    ]);

    Livewire::actingAs($admin)
        ->test(OrderManager::class)
        ->set('search', "' OR 1=1 --")
        ->assertDontSee('Cliente legítimo')
        ->assertDontSee('legitimo@example.com');
});
