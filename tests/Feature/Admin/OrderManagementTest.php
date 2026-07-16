<?php

use App\Livewire\Admin\OrderDetail;
use App\Livewire\Admin\OrderManager;
use App\Models\Card;
use App\Models\CardSet;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\User;
use Livewire\Livewire;

it('redirige a los visitantes al login al acceder a pedidos administrativos', function () {
    $this->get('/admin/pedidos')
        ->assertRedirect(route('login'));
});

it('redirige a los usuarios normales fuera de pedidos administrativos', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $this->actingAs($user)
        ->get(route('admin.pedidos'))
        ->assertRedirect(route('storefront.catalog'));
});

it('permite que un administrador acceda a pedidos administrativos', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.pedidos'))
        ->assertOk()
        ->assertSee('Administración de pedidos');
});

it('muestra los pedidos en el listado administrativo', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $order = Order::create([
        'user_id' => null,
        'status' => 'pending',
        'customer_name' => 'Cliente del listado',
        'customer_email' => 'listado@example.com',
        'customer_phone' => null,
        'shipping_address_line_1' => 'Avenida Listado 123',
        'shipping_address_line_2' => null,
        'shipping_city' => 'Santiago',
        'shipping_region' => 'Región Metropolitana',
        'shipping_postal_code' => null,
        'shipping_country' => 'Chile',
        'subtotal' => 23800,
        'tax_total' => 3800,
        'shipping_total' => 0,
        'total' => 23800,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.pedidos'))
        ->assertOk()
        ->assertSee("Pedido #{$order->id}")
        ->assertSee('Cliente del listado')
        ->assertSee('listado@example.com')
        ->assertSee('Pendiente')
        ->assertSee('$23.800')
        ->assertSeeHtml(
            'href="'.route('admin.pedidos.show', $order).'"',
        );
});

it('busca pedidos por nombre o correo del cliente', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $baseOrder = [
        'user_id' => null,
        'status' => 'pending',
        'customer_phone' => null,
        'shipping_address_line_1' => 'Avenida de prueba 123',
        'shipping_address_line_2' => null,
        'shipping_city' => 'Santiago',
        'shipping_region' => 'Región Metropolitana',
        'shipping_postal_code' => null,
        'shipping_country' => 'Chile',
        'subtotal' => 11900,
        'tax_total' => 1900,
        'shipping_total' => 0,
        'total' => 11900,
    ];

    Order::create([
        ...$baseOrder,
        'customer_name' => 'Cliente Alfa',
        'customer_email' => 'alfa@example.com',
    ]);

    Order::create([
        ...$baseOrder,
        'customer_name' => 'Cliente Beta',
        'customer_email' => 'beta@example.com',
    ]);

    $this->actingAs($admin);

    Livewire::test(OrderManager::class)
        ->set('search', 'alfa@example.com')
        ->assertSee('Cliente Alfa')
        ->assertDontSee('Cliente Beta');
});

it('busca pedidos por número', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $baseOrder = [
        'user_id' => null,
        'status' => 'pending',
        'customer_phone' => null,
        'shipping_address_line_1' => 'Avenida de prueba 123',
        'shipping_address_line_2' => null,
        'shipping_city' => 'Santiago',
        'shipping_region' => 'Región Metropolitana',
        'shipping_postal_code' => null,
        'shipping_country' => 'Chile',
        'subtotal' => 11900,
        'tax_total' => 1900,
        'shipping_total' => 0,
        'total' => 11900,
    ];

    $targetOrder = Order::create([
        ...$baseOrder,
        'customer_name' => 'Pedido buscado',
        'customer_email' => 'buscado@example.com',
    ]);

    $otherOrder = Order::create([
        ...$baseOrder,
        'customer_name' => 'Pedido diferente',
        'customer_email' => 'diferente@example.com',
    ]);

    $this->actingAs($admin);

    Livewire::test(OrderManager::class)
        ->set('search', "#{$targetOrder->id}")
        ->assertSee("Pedido #{$targetOrder->id}")
        ->assertDontSee("Pedido #{$otherOrder->id}");
});

it('filtra pedidos por estado', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $baseOrder = [
        'user_id' => null,
        'customer_phone' => null,
        'shipping_address_line_1' => 'Avenida de prueba 123',
        'shipping_address_line_2' => null,
        'shipping_city' => 'Santiago',
        'shipping_region' => 'Región Metropolitana',
        'shipping_postal_code' => null,
        'shipping_country' => 'Chile',
        'subtotal' => 11900,
        'tax_total' => 1900,
        'shipping_total' => 0,
        'total' => 11900,
    ];

    Order::create([
        ...$baseOrder,
        'status' => 'pending',
        'customer_name' => 'Cliente pendiente',
        'customer_email' => 'pendiente@example.com',
    ]);

    Order::create([
        ...$baseOrder,
        'status' => 'cancelled',
        'customer_name' => 'Cliente cancelado',
        'customer_email' => 'cancelado@example.com',
    ]);

    $this->actingAs($admin);

    Livewire::test(OrderManager::class)
        ->set('filterStatus', 'cancelled')
        ->assertSee('Cliente cancelado')
        ->assertDontSee('Cliente pendiente');
});

it('muestra el detalle completo de un pedido', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $order = Order::create([
        'user_id' => null,
        'status' => 'pending',
        'customer_name' => 'Cliente detalle',
        'customer_email' => 'detalle@example.com',
        'customer_phone' => '+56 9 1234 5678',
        'shipping_address_line_1' => 'Avenida Detalle 456',
        'shipping_address_line_2' => 'Departamento 12',
        'shipping_city' => 'Providencia',
        'shipping_region' => 'Región Metropolitana',
        'shipping_postal_code' => '7500000',
        'shipping_country' => 'Chile',
        'subtotal' => 23800,
        'tax_total' => 3800,
        'shipping_total' => 0,
        'total' => 23800,
    ]);

    $order->items()->create([
        'inventory_id' => null,
        'card_name' => 'Carta del detalle',
        'set_name' => 'Set del detalle',
        'card_number' => '025',
        'image_url' => null,
        'language' => 'Español',
        'condition' => 'Near Mint (NM)',
        'variant' => 'Holo',
        'unit_price' => 11900,
        'quantity' => 2,
        'subtotal' => 23800,
        'tax_rate' => 1900,
        'tax_total' => 3800,
    ]);

    $this->actingAs($admin)
        ->get("/admin/pedidos/{$order->id}")
        ->assertOk()
        ->assertSee("Pedido #{$order->id}")
        ->assertSee('Cliente detalle')
        ->assertSee('detalle@example.com')
        ->assertSee('+56 9 1234 5678')
        ->assertSee('Avenida Detalle 456')
        ->assertSee('Providencia')
        ->assertSee('Carta del detalle')
        ->assertSee('Set del detalle')
        ->assertSee('2 unidades')
        ->assertSee('$23.800');
});

it('cancela un pedido pendiente y restaura su inventario', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $cardSet = CardSet::create([
        'name' => 'Set cancelación',
        'set_total' => 100,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Carta cancelada',
        'card_number' => '025',
    ]);

    $inventory = Inventory::create([
        'card_id' => $card->id,
        'language' => 'Español',
        'condition' => 'Near Mint (NM)',
        'variant' => 'Holo',
        'price' => 11900,
        'stock' => 3,
        'is_active' => true,
    ]);

    $order = Order::create([
        'user_id' => null,
        'status' => 'pending',
        'customer_name' => 'Cliente cancelación',
        'customer_email' => 'cancelacion@example.com',
        'customer_phone' => null,
        'shipping_address_line_1' => 'Avenida Cancelación 123',
        'shipping_address_line_2' => null,
        'shipping_city' => 'Santiago',
        'shipping_region' => 'Región Metropolitana',
        'shipping_postal_code' => null,
        'shipping_country' => 'Chile',
        'subtotal' => 23800,
        'tax_total' => 3800,
        'shipping_total' => 0,
        'total' => 23800,
    ]);

    $order->items()->create([
        'inventory_id' => $inventory->id,
        'card_name' => $card->name,
        'set_name' => $cardSet->name,
        'card_number' => $card->card_number,
        'image_url' => null,
        'language' => $inventory->language,
        'condition' => $inventory->condition,
        'variant' => $inventory->variant,
        'unit_price' => 11900,
        'quantity' => 2,
        'subtotal' => 23800,
        'tax_rate' => 1900,
        'tax_total' => 3800,
    ]);

    $this->actingAs($admin);

    Livewire::test(OrderDetail::class, [
        'order' => $order,
    ])
        ->call('cancelOrder')
        ->assertSee('Pedido cancelado y stock restaurado.')
        ->assertSee('Cancelado')
        ->assertDontSee('Cancelar pedido');

    expect($order->refresh()->status)->toBe('cancelled')
        ->and($inventory->refresh()->stock)->toBe(5);
});

it('impide cancelar dos veces el mismo pedido', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $cardSet = CardSet::create([
        'name' => 'Set cancelación duplicada',
        'set_total' => 100,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Carta cancelación duplicada',
        'card_number' => '026',
    ]);

    $inventory = Inventory::create([
        'card_id' => $card->id,
        'language' => 'Español',
        'condition' => 'Near Mint (NM)',
        'variant' => 'Normal',
        'price' => 11900,
        'stock' => 3,
        'is_active' => true,
    ]);

    $order = Order::create([
        'user_id' => null,
        'status' => 'pending',
        'customer_name' => 'Cliente cancelación duplicada',
        'customer_email' => 'duplicada@example.com',
        'customer_phone' => null,
        'shipping_address_line_1' => 'Avenida Duplicada 123',
        'shipping_address_line_2' => null,
        'shipping_city' => 'Santiago',
        'shipping_region' => 'Región Metropolitana',
        'shipping_postal_code' => null,
        'shipping_country' => 'Chile',
        'subtotal' => 23800,
        'tax_total' => 3800,
        'shipping_total' => 0,
        'total' => 23800,
    ]);

    $order->items()->create([
        'inventory_id' => $inventory->id,
        'card_name' => $card->name,
        'set_name' => $cardSet->name,
        'card_number' => $card->card_number,
        'image_url' => null,
        'language' => $inventory->language,
        'condition' => $inventory->condition,
        'variant' => $inventory->variant,
        'unit_price' => 11900,
        'quantity' => 2,
        'subtotal' => 23800,
        'tax_rate' => 1900,
        'tax_total' => 3800,
    ]);

    $this->actingAs($admin);

    $component = Livewire::test(OrderDetail::class, [
        'order' => $order,
    ]);

    $component
        ->call('cancelOrder')
        ->call('cancelOrder')
        ->assertHasErrors([
            'order' => [
                'Solo los pedidos pendientes pueden cancelarse.',
            ],
        ]);

    expect($order->refresh()->status)->toBe('cancelled')
        ->and($inventory->refresh()->stock)->toBe(5);
});

it('redirige a los visitantes al login al acceder al detalle de un pedido', function () {
    $order = Order::create([
        'user_id' => null,
        'status' => 'pending',
        'customer_name' => 'Cliente protegido',
        'customer_email' => 'protegido@example.com',
        'customer_phone' => null,
        'shipping_address_line_1' => 'Avenida Protegida 123',
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

    $this->get(route('admin.pedidos.show', $order))
        ->assertRedirect(route('login'));
});

it('redirige a los usuarios normales fuera del detalle de un pedido', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $order = Order::create([
        'user_id' => null,
        'status' => 'pending',
        'customer_name' => 'Cliente restringido',
        'customer_email' => 'restringido@example.com',
        'customer_phone' => null,
        'shipping_address_line_1' => 'Avenida Restringida 123',
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

    $this->actingAs($user)
        ->get(route('admin.pedidos.show', $order))
        ->assertRedirect(route('storefront.catalog'));
});

it('impide que un usuario normal monte directamente el gestor de pedidos', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    Livewire::actingAs($user)
        ->test(OrderManager::class)
        ->assertForbidden();
});

it('impide que un usuario normal monte directamente el detalle de un pedido', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $order = Order::create([
        'user_id' => null,
        'status' => 'pending',
        'customer_name' => 'Cliente protegido por componente',
        'customer_email' => 'componente@example.com',
        'customer_phone' => null,
        'shipping_address_line_1' => 'Avenida Componente 123',
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

    Livewire::actingAs($user)
        ->test(OrderDetail::class, [
            'order' => $order,
        ])
        ->assertForbidden();
});

it('muestra navegación entre inventario y pedidos', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.pedidos'))
        ->assertOk()
        ->assertSeeHtml(
            'href="'.route('admin.inventario').'"',
        );

    $this->actingAs($admin)
        ->get(route('admin.inventario'))
        ->assertOk()
        ->assertSeeHtml(
            'href="'.route('admin.pedidos').'"',
        );
});
