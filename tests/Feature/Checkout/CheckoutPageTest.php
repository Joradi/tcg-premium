<?php

use App\Livewire\Storefront\Checkout;
use App\Models\Card;
use App\Models\CardSet;
use App\Models\Cart;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

it('muestra la página de checkout', function () {
    $this->get('/checkout')
        ->assertOk()
        ->assertSee('Finalizar compra')
        ->assertSee('Resumen del pedido');
});

it('muestra los productos y el total del carrito activo', function () {
    $cardSet = CardSet::create([
        'name' => 'Set checkout visible',
        'set_total' => 100,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Carta checkout visible',
        'card_number' => '025',
    ]);

    $inventory = Inventory::create([
        'card_id' => $card->id,
        'language' => 'Español',
        'condition' => 'Near Mint (NM)',
        'variant' => 'Holo',
        'price' => 11900,
        'stock' => 4,
        'is_active' => true,
    ]);

    $cart = Cart::create([
        'user_id' => null,
        'session_id' => session()->getId(),
    ]);

    $cart->items()->create([
        'inventory_id' => $inventory->id,
        'quantity' => 2,
    ]);

    Livewire::test(Checkout::class)
        ->assertSee('Carta checkout visible')
        ->assertSee('2 unidades')
        ->assertSee('$23.800');
});

it('valida los datos obligatorios del checkout', function () {
    Livewire::test(Checkout::class)
        ->call('submit')
        ->assertHasErrors([
            'customerName' => 'required',
            'customerEmail' => 'required',
            'shippingAddressLine1' => 'required',
            'shippingCity' => 'required',
            'shippingRegion' => 'required',
        ]);
});

it('crea un pedido al enviar datos válidos desde el checkout', function () {
    $cardSet = CardSet::create([
        'name' => 'Set compra desde checkout',
        'set_total' => 100,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Carta comprada desde checkout',
        'card_number' => '050',
    ]);

    $inventory = Inventory::create([
        'card_id' => $card->id,
        'language' => 'Español',
        'condition' => 'Near Mint (NM)',
        'variant' => 'Holo',
        'price' => 11900,
        'stock' => 4,
        'is_active' => true,
    ]);

    $cart = Cart::create([
        'user_id' => null,
        'session_id' => session()->getId(),
    ]);

    $cart->items()->create([
        'inventory_id' => $inventory->id,
        'quantity' => 2,
    ]);

    Livewire::test(Checkout::class)
        ->set('customerName', 'Cliente checkout')
        ->set('customerEmail', 'checkout@example.com')
        ->set('customerPhone', '+56 9 1234 5678')
        ->set('shippingAddressLine1', 'Avenida Checkout 123')
        ->set('shippingAddressLine2', 'Departamento 45')
        ->set('shippingCity', 'Santiago')
        ->set('shippingRegion', 'Región Metropolitana')
        ->set('shippingPostalCode', '8320000')
        ->set('shippingCountry', 'Chile')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('storefront.checkout.confirmation'));

    $order = Order::query()->firstOrFail();

    expect($order->status)->toBe('pending')
        ->and($order->user_id)->toBeNull()
        ->and($order->customer_name)->toBe('Cliente checkout')
        ->and($order->customer_email)->toBe('checkout@example.com')
        ->and($order->subtotal)->toBe(23800)
        ->and($order->tax_total)->toBe(3800)
        ->and($order->shipping_total)->toBe(0)
        ->and($order->total)->toBe(23800)
        ->and($inventory->refresh()->stock)->toBe(2)
        ->and($cart->items()->count())->toBe(0);

    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'inventory_id' => $inventory->id,
        'card_name' => 'Carta comprada desde checkout',
        'unit_price' => 11900,
        'quantity' => 2,
        'subtotal' => 23800,
        'tax_total' => 3800,
    ]);
});

it('muestra los datos del pedido completado en la confirmación', function () {
    $order = Order::create([
        'user_id' => null,
        'status' => 'pending',
        'customer_name' => 'Cliente confirmado',
        'customer_email' => 'confirmado@example.com',
        'customer_phone' => null,
        'shipping_address_line_1' => 'Avenida Confirmación 123',
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
        'inventory_id' => null,
        'card_name' => 'Carta confirmada',
        'set_name' => 'Set confirmado',
        'card_number' => '050',
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

    $this->withSession([
        'checkout.completed_order_id' => $order->id,
    ])->get(route('storefront.checkout.confirmation'))
        ->assertOk()
        ->assertSee("Pedido #{$order->id}")
        ->assertSee('Cliente confirmado')
        ->assertSee('confirmado@example.com')
        ->assertSee('Carta confirmada')
        ->assertSee('$23.800');
});

it('no crea un pedido cuando no existe un carrito activo', function () {
    Livewire::test(Checkout::class)
        ->set('customerName', 'Cliente sin carrito')
        ->set('customerEmail', 'sin-carrito@example.com')
        ->set('shippingAddressLine1', 'Avenida vacía 123')
        ->set('shippingCity', 'Santiago')
        ->set('shippingRegion', 'Región Metropolitana')
        ->call('submit')
        ->assertHasErrors('cart', 'El carrito está vacío.');

    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseCount('order_items', 0);
});

it('muestra un error cuando el stock cambia antes de confirmar el pedido', function () {
    $cardSet = CardSet::create([
        'name' => 'Set stock modificado',
        'set_total' => 100,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Carta sin stock al confirmar',
        'card_number' => '060',
    ]);

    $inventory = Inventory::create([
        'card_id' => $card->id,
        'language' => 'Español',
        'condition' => 'Near Mint (NM)',
        'variant' => 'Normal',
        'price' => 11900,
        'stock' => 1,
        'is_active' => true,
    ]);

    $cart = Cart::create([
        'user_id' => null,
        'session_id' => session()->getId(),
    ]);

    $cartItem = $cart->items()->create([
        'inventory_id' => $inventory->id,
        'quantity' => 2,
    ]);

    Livewire::test(Checkout::class)
        ->set('customerName', 'Cliente sin stock')
        ->set('customerEmail', 'stock@example.com')
        ->set('shippingAddressLine1', 'Avenida Stock 123')
        ->set('shippingCity', 'Santiago')
        ->set('shippingRegion', 'Región Metropolitana')
        ->call('submit')
        ->assertHasErrors([
            'cart' => [
                'No existe stock suficiente para completar el pedido.',
            ],
        ])
        ->assertSee(
            'No existe stock suficiente para completar el pedido.',
        );

    expect($inventory->refresh()->stock)->toBe(1)
        ->and($cartItem->refresh()->quantity)->toBe(2)
        ->and($cart->items()->count())->toBe(1);

    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseCount('order_items', 0);
});

it('precarga el nombre y correo del usuario autenticado', function () {
    $user = User::factory()->create([
        'name' => 'Jonathan Cliente',
        'email' => 'jonathan@example.com',
    ]);

    $this->actingAs($user);

    Livewire::test(Checkout::class)
        ->assertSet('customerName', 'Jonathan Cliente')
        ->assertSet('customerEmail', 'jonathan@example.com');
});

it('impide acceder a la confirmación sin un pedido completado', function () {
    $this->get(route('storefront.checkout.confirmation'))
        ->assertNotFound();
});

it('bloquea temporalmente demasiados intentos de finalizar compra', function () {
    $cardSet = CardSet::create([
        'name' => 'Set checkout limitado',
        'set_total' => 100,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Carta checkout limitado',
        'card_number' => '070',
    ]);

    $inventory = Inventory::create([
        'card_id' => $card->id,
        'language' => 'Español',
        'condition' => 'Near Mint (NM)',
        'variant' => 'Normal',
        'price' => 11900,
        'stock' => 4,
        'is_active' => true,
    ]);

    $cart = Cart::create([
        'user_id' => null,
        'session_id' => session()->getId(),
    ]);

    $cart->items()->create([
        'inventory_id' => $inventory->id,
        'quantity' => 2,
    ]);

    $rateLimitKey = sprintf(
        'checkout-submit:guest:%s|127.0.0.1',
        $cart->session_id,
    );

    RateLimiter::clear($rateLimitKey);

    foreach (range(1, 5) as $_) {
        RateLimiter::hit($rateLimitKey, 60);
    }

    Livewire::test(Checkout::class)
        ->set('customerName', 'Cliente limitado')
        ->set('customerEmail', 'limitado@example.com')
        ->set('shippingAddressLine1', 'Avenida Límite 123')
        ->set('shippingCity', 'Santiago')
        ->set('shippingRegion', 'Región Metropolitana')
        ->call('submit')
        ->assertHasErrors([
            'cart' => [
                'Demasiados intentos de compra. Espera un minuto antes de volver a intentarlo.',
            ],
        ]);

    expect($inventory->refresh()->stock)->toBe(4)
        ->and($cart->items()->count())->toBe(1);

    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseCount('order_items', 0);

    RateLimiter::clear($rateLimitKey);
});
