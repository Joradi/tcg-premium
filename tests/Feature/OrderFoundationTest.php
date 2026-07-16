<?php

use App\Models\Card;
use App\Models\CardSet;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;

it('relaciona un pedido con su usuario y conserva sus montos', function () {
    $user = User::factory()->create();

    $order = Order::create([
        'user_id' => $user->id,
        'status' => 'pending',
        'customer_name' => 'Jona Díaz',
        'customer_email' => 'jona@example.com',
        'customer_phone' => null,
        'shipping_address_line_1' => 'Avenida de prueba 123',
        'shipping_address_line_2' => null,
        'shipping_city' => 'Santiago',
        'shipping_region' => 'Región Metropolitana',
        'shipping_postal_code' => null,
        'shipping_country' => 'Chile',
        'subtotal' => 11900,
        'tax_total' => 1900,
        'shipping_total' => 3000,
        'total' => 14900,
    ]);

    expect($order->user->is($user))->toBeTrue()
        ->and($user->orders()->first()->is($order))->toBeTrue()
        ->and($order->subtotal)->toBe(11900)
        ->and($order->tax_total)->toBe(1900)
        ->and($order->shipping_total)->toBe(3000)
        ->and($order->total)->toBe(14900);

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'user_id' => $user->id,
        'status' => 'pending',
        'subtotal' => 11900,
        'tax_total' => 1900,
        'shipping_total' => 3000,
        'total' => 14900,
    ]);
});

it('conserva una fotografía histórica del producto comprado', function () {
    $cardSet = CardSet::create([
        'name' => 'Set histórico',
        'set_total' => 100,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Pikachu histórico',
        'card_number' => '025',
        'image_url' => 'https://example.com/pikachu.png',
    ]);

    $inventory = Inventory::create([
        'card_id' => $card->id,
        'language' => 'Japonés',
        'condition' => 'Near Mint (NM)',
        'variant' => 'Holo',
        'price' => 11900,
        'stock' => 4,
        'is_active' => true,
    ]);

    $order = Order::create([
        'status' => 'pending',
        'customer_name' => 'Cliente invitado',
        'customer_email' => 'cliente@example.com',
        'shipping_address_line_1' => 'Calle de prueba 456',
        'shipping_city' => 'Santiago',
        'shipping_region' => 'Región Metropolitana',
        'shipping_country' => 'Chile',
        'subtotal' => 23800,
        'tax_total' => 3800,
        'shipping_total' => 0,
        'total' => 23800,
    ]);

    $item = OrderItem::create([
        'order_id' => $order->id,
        'inventory_id' => $inventory->id,
        'card_name' => $card->name,
        'set_name' => $cardSet->name,
        'card_number' => $card->card_number,
        'image_url' => $card->image_url,
        'language' => $inventory->language,
        'condition' => $inventory->condition,
        'variant' => $inventory->variant,
        'unit_price' => 11900,
        'quantity' => 2,
        'subtotal' => 23800,
        'tax_rate' => 1900,
        'tax_total' => 3800,
    ]);

    expect($item->order->is($order))->toBeTrue()
        ->and($order->items()->first()->is($item))->toBeTrue()
        ->and($item->inventory->is($inventory))->toBeTrue()
        ->and($item->card_name)->toBe('Pikachu histórico')
        ->and($item->variant)->toBe('Holo')
        ->and($item->unit_price)->toBe(11900)
        ->and($item->quantity)->toBe(2)
        ->and($item->subtotal)->toBe(23800)
        ->and($item->tax_rate)->toBe(1900)
        ->and($item->tax_total)->toBe(3800);
});

it('conserva el pedido aunque se eliminen el usuario y el inventario original', function () {
    $user = User::factory()->create();

    $cardSet = CardSet::create([
        'name' => 'Set eliminable',
        'set_total' => 80,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Carta histórica eliminable',
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

    $order = Order::create([
        'user_id' => $user->id,
        'status' => 'pending',
        'customer_name' => $user->name,
        'customer_email' => $user->email,
        'shipping_address_line_1' => 'Dirección histórica 123',
        'shipping_city' => 'Santiago',
        'shipping_region' => 'Región Metropolitana',
        'shipping_country' => 'Chile',
        'subtotal' => 11900,
        'tax_total' => 1900,
        'shipping_total' => 0,
        'total' => 11900,
    ]);

    $item = OrderItem::create([
        'order_id' => $order->id,
        'inventory_id' => $inventory->id,
        'card_name' => $card->name,
        'set_name' => $cardSet->name,
        'card_number' => $card->card_number,
        'language' => $inventory->language,
        'condition' => $inventory->condition,
        'variant' => $inventory->variant,
        'unit_price' => 11900,
        'quantity' => 1,
        'subtotal' => 11900,
        'tax_rate' => 1900,
        'tax_total' => 1900,
    ]);

    $user->delete();
    $inventory->delete();

    $order->refresh();
    $item->refresh();

    expect($order->user_id)->toBeNull()
        ->and($item->inventory_id)->toBeNull()
        ->and($item->card_name)->toBe('Carta histórica eliminable')
        ->and($item->unit_price)->toBe(11900)
        ->and($item->tax_total)->toBe(1900);

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'user_id' => null,
    ]);

    $this->assertDatabaseHas('order_items', [
        'id' => $item->id,
        'inventory_id' => null,
        'card_name' => 'Carta histórica eliminable',
    ]);
});
