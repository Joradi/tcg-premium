<?php

use App\Actions\Checkout\CreateOrderFromCart;
use App\Models\Card;
use App\Models\CardSet;
use App\Models\Cart;
use App\Models\Inventory;
use App\Models\User;

it('crea un pedido de invitado desde el carrito y descuenta el inventario', function () {
    $cardSet = CardSet::create([
        'name' => 'Set checkout',
        'set_total' => 100,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Carta checkout',
        'card_number' => '025',
        'image_url' => 'https://example.com/card.png',
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
        'session_id' => 'guest-session-checkout',
    ]);

    $cart->items()->create([
        'inventory_id' => $inventory->id,
        'quantity' => 2,
    ]);

    $order = app(CreateOrderFromCart::class)->handle($cart, [
        'customer_name' => 'Cliente invitado',
        'customer_email' => 'invitado@example.com',
        'customer_phone' => null,
        'shipping_address_line_1' => 'Avenida de prueba 123',
        'shipping_address_line_2' => null,
        'shipping_city' => 'Santiago',
        'shipping_region' => 'Región Metropolitana',
        'shipping_postal_code' => null,
        'shipping_country' => 'Chile',
        'shipping_total' => 0,
    ]);

    $item = $order->items()->firstOrFail();

    expect($order->user_id)->toBeNull()
        ->and($order->status)->toBe('pending')
        ->and($order->subtotal)->toBe(23800)
        ->and($order->tax_total)->toBe(3800)
        ->and($order->shipping_total)->toBe(0)
        ->and($order->total)->toBe(23800)
        ->and($item->inventory_id)->toBe($inventory->id)
        ->and($item->card_name)->toBe('Carta checkout')
        ->and($item->set_name)->toBe('Set checkout')
        ->and($item->language)->toBe('Español')
        ->and($item->condition)->toBe('Near Mint (NM)')
        ->and($item->variant)->toBe('Holo')
        ->and($item->unit_price)->toBe(11900)
        ->and($item->quantity)->toBe(2)
        ->and($item->subtotal)->toBe(23800)
        ->and($item->tax_rate)->toBe(1900)
        ->and($item->tax_total)->toBe(3800)
        ->and($inventory->refresh()->stock)->toBe(2)
        ->and($cart->items()->count())->toBe(0);
});

it('rechaza el pedido cuando no hay stock y no deja cambios parciales', function () {
    $cardSet = CardSet::create([
        'name' => 'Set sin stock',
        'set_total' => 50,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Carta con stock insuficiente',
        'card_number' => '010',
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
        'session_id' => 'guest-session-without-stock',
    ]);

    $cartItem = $cart->items()->create([
        'inventory_id' => $inventory->id,
        'quantity' => 2,
    ]);

    expect(fn () => app(CreateOrderFromCart::class)->handle($cart, [
        'customer_name' => 'Cliente sin stock',
        'customer_email' => 'cliente@example.com',
        'shipping_address_line_1' => 'Calle de prueba 123',
        'shipping_city' => 'Santiago',
        'shipping_region' => 'Región Metropolitana',
        'shipping_country' => 'Chile',
        'shipping_total' => 0,
    ]))->toThrow(
        DomainException::class,
        'No existe stock suficiente para completar el pedido.',
    );

    expect($inventory->refresh()->stock)->toBe(1)
        ->and($cartItem->refresh()->quantity)->toBe(2)
        ->and($cart->items()->count())->toBe(1);

    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseCount('order_items', 0);
});

it('asocia el pedido al usuario propietario del carrito', function () {
    $user = User::factory()->create();

    $cardSet = CardSet::create([
        'name' => 'Set usuario autenticado',
        'set_total' => 40,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Carta usuario autenticado',
        'card_number' => '020',
    ]);

    $inventory = Inventory::create([
        'card_id' => $card->id,
        'language' => 'Español',
        'condition' => 'Near Mint (NM)',
        'variant' => 'Normal',
        'price' => 11900,
        'stock' => 2,
        'is_active' => true,
    ]);

    $cart = Cart::create([
        'user_id' => $user->id,
        'session_id' => null,
    ]);

    $cart->items()->create([
        'inventory_id' => $inventory->id,
        'quantity' => 1,
    ]);

    $order = app(CreateOrderFromCart::class)->handle($cart, [
        'customer_name' => $user->name,
        'customer_email' => $user->email,
        'shipping_address_line_1' => 'Calle autenticada 123',
        'shipping_city' => 'Santiago',
        'shipping_region' => 'Región Metropolitana',
    ]);

    expect($order->user_id)->toBe($user->id)
        ->and($order->user->is($user))->toBeTrue()
        ->and($order->shipping_country)->toBe('Chile')
        ->and($order->shipping_total)->toBe(0)
        ->and($order->subtotal)->toBe(11900)
        ->and($order->tax_total)->toBe(1900)
        ->and($order->total)->toBe(11900)
        ->and($inventory->refresh()->stock)->toBe(1)
        ->and($cart->items()->count())->toBe(0);
});

it('rechaza la creación de un pedido desde un carrito vacío', function () {
    $cart = Cart::create([
        'user_id' => null,
        'session_id' => 'guest-empty-cart',
    ]);

    expect(fn () => app(CreateOrderFromCart::class)->handle($cart, [
        'customer_name' => 'Cliente sin productos',
        'customer_email' => 'vacio@example.com',
        'shipping_address_line_1' => 'Calle vacía 123',
        'shipping_city' => 'Santiago',
        'shipping_region' => 'Región Metropolitana',
    ]))->toThrow(
        DomainException::class,
        'El carrito está vacío.',
    );

    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseCount('order_items', 0);
});

it('calcula los totales de un pedido con varios productos', function () {
    $cardSet = CardSet::create([
        'name' => 'Set múltiples productos',
        'set_total' => 100,
    ]);

    $firstCard = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Primera carta',
        'card_number' => '001',
    ]);

    $secondCard = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Segunda carta',
        'card_number' => '002',
    ]);

    $firstInventory = Inventory::create([
        'card_id' => $firstCard->id,
        'language' => 'Español',
        'condition' => 'Near Mint (NM)',
        'variant' => 'Holo',
        'price' => 11900,
        'stock' => 5,
        'is_active' => true,
    ]);

    $secondInventory = Inventory::create([
        'card_id' => $secondCard->id,
        'language' => 'Inglés',
        'condition' => 'Excellent (EX)',
        'variant' => 'Normal',
        'price' => 5950,
        'stock' => 3,
        'is_active' => true,
    ]);

    $cart = Cart::create([
        'user_id' => null,
        'session_id' => 'guest-multiple-products',
    ]);

    $cart->items()->createMany([
        [
            'inventory_id' => $firstInventory->id,
            'quantity' => 2,
        ],
        [
            'inventory_id' => $secondInventory->id,
            'quantity' => 1,
        ],
    ]);

    $order = app(CreateOrderFromCart::class)->handle($cart, [
        'customer_name' => 'Cliente múltiples productos',
        'customer_email' => 'multiple@example.com',
        'shipping_address_line_1' => 'Calle múltiple 123',
        'shipping_city' => 'Santiago',
        'shipping_region' => 'Región Metropolitana',
    ]);

    $items = $order->items->keyBy('inventory_id');

    expect($order->items)->toHaveCount(2)
        ->and($order->subtotal)->toBe(29750)
        ->and($order->tax_total)->toBe(4750)
        ->and($order->total)->toBe(29750)
        ->and($items[$firstInventory->id]->subtotal)->toBe(23800)
        ->and($items[$firstInventory->id]->tax_total)->toBe(3800)
        ->and($items[$secondInventory->id]->subtotal)->toBe(5950)
        ->and($items[$secondInventory->id]->tax_total)->toBe(950)
        ->and($firstInventory->refresh()->stock)->toBe(3)
        ->and($secondInventory->refresh()->stock)->toBe(2)
        ->and($cart->items()->count())->toBe(0);
});

it('rechaza productos desactivados sin modificar el carrito ni el inventario', function () {
    $cardSet = CardSet::create([
        'name' => 'Set producto desactivado',
        'set_total' => 20,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Carta desactivada',
        'card_number' => '015',
    ]);

    $inventory = Inventory::create([
        'card_id' => $card->id,
        'language' => 'Español',
        'condition' => 'Near Mint (NM)',
        'variant' => 'Normal',
        'price' => 11900,
        'stock' => 3,
        'is_active' => false,
    ]);

    $cart = Cart::create([
        'user_id' => null,
        'session_id' => 'guest-inactive-product',
    ]);

    $cartItem = $cart->items()->create([
        'inventory_id' => $inventory->id,
        'quantity' => 1,
    ]);

    expect(fn () => app(CreateOrderFromCart::class)->handle($cart, [
        'customer_name' => 'Cliente producto desactivado',
        'customer_email' => 'inactive@example.com',
        'shipping_address_line_1' => 'Calle desactivada 123',
        'shipping_city' => 'Santiago',
        'shipping_region' => 'Región Metropolitana',
    ]))->toThrow(
        DomainException::class,
        'Uno de los productos ya no está disponible.',
    );

    expect($inventory->refresh()->stock)->toBe(3)
        ->and($cartItem->refresh()->quantity)->toBe(1)
        ->and($cart->items()->count())->toBe(1);

    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseCount('order_items', 0);
});

it('rechaza cantidades menores que uno sin crear el pedido', function () {
    $cardSet = CardSet::create([
        'name' => 'Set cantidad inválida',
        'set_total' => 20,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Carta cantidad inválida',
        'card_number' => '016',
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

    $cart = Cart::create([
        'user_id' => null,
        'session_id' => 'guest-invalid-quantity',
    ]);

    $cartItem = $cart->items()->create([
        'inventory_id' => $inventory->id,
        'quantity' => 0,
    ]);

    expect(fn () => app(CreateOrderFromCart::class)->handle($cart, [
        'customer_name' => 'Cliente cantidad inválida',
        'customer_email' => 'quantity@example.com',
        'shipping_address_line_1' => 'Calle cantidad 123',
        'shipping_city' => 'Santiago',
        'shipping_region' => 'Región Metropolitana',
    ]))->toThrow(
        DomainException::class,
        'La cantidad del producto debe ser mayor que cero.',
    );

    expect($inventory->refresh()->stock)->toBe(3)
        ->and($cartItem->refresh()->quantity)->toBe(0)
        ->and($cart->items()->count())->toBe(1);

    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseCount('order_items', 0);
});

it('rechaza un costo de despacho negativo sin modificar el pedido', function () {
    $cardSet = CardSet::create([
        'name' => 'Set despacho inválido',
        'set_total' => 20,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Carta despacho inválido',
        'card_number' => '017',
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

    $cart = Cart::create([
        'user_id' => null,
        'session_id' => 'guest-negative-shipping',
    ]);

    $cartItem = $cart->items()->create([
        'inventory_id' => $inventory->id,
        'quantity' => 1,
    ]);

    expect(fn () => app(CreateOrderFromCart::class)->handle($cart, [
        'customer_name' => 'Cliente despacho inválido',
        'customer_email' => 'shipping@example.com',
        'shipping_address_line_1' => 'Calle despacho 123',
        'shipping_city' => 'Santiago',
        'shipping_region' => 'Región Metropolitana',
        'shipping_total' => -1,
    ]))->toThrow(
        DomainException::class,
        'El costo de despacho no puede ser negativo.',
    );

    expect($inventory->refresh()->stock)->toBe(3)
        ->and($cartItem->refresh()->quantity)->toBe(1)
        ->and($cart->items()->count())->toBe(1);

    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseCount('order_items', 0);
});
