<?php

use App\Models\Order;
use App\Models\User;

it('escapa contenido almacenado al mostrar pedidos administrativos', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $payload = '<script>alert(1)</script>';

    $order = Order::create([
        'user_id' => null,
        'status' => 'pending',
        'customer_name' => $payload,
        'customer_email' => 'xss@example.com',
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

    $escapedPayload = '&lt;script&gt;alert(1)&lt;/script&gt;';

    $this->actingAs($admin)
        ->get(route('admin.pedidos'))
        ->assertOk()
        ->assertDontSee($payload, false)
        ->assertSee($escapedPayload, false);

    $this->actingAs($admin)
        ->get(route('admin.pedidos.show', $order))
        ->assertOk()
        ->assertDontSee($payload, false)
        ->assertSee($escapedPayload, false);
});
