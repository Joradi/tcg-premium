<?php

use App\Livewire\Storefront\Catalog;
use App\Models\Card;
use App\Models\CardSet;
use App\Models\Cart;
use App\Models\Inventory;
use Livewire\Livewire;

it('permite que un visitante agregue un producto al carrito', function () {
    $cardSet = CardSet::create([
        'name' => 'Set de prueba',
        'set_total' => 100,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Pikachu de prueba',
        'card_number' => '025',
    ]);

    $inventory = Inventory::create([
        'card_id' => $card->id,
        'price' => 15000,
        'stock' => 3,
        'is_active' => true,
    ]);

    Livewire::test(Catalog::class)
        ->call('addToCart', $inventory->id)
        ->assertSet('cartMessage', 'Producto agregado al carrito')
        ->assertSet('cartMessageType', 'success')
        ->assertDispatched(
            'cart-notification',
            message: 'Producto agregado al carrito',
            type: 'success')
        ->assertDispatched('cart-updated');

    $this->assertDatabaseCount('carts', 1);
    $this->assertDatabaseCount('cart_items', 1);

    $this->assertDatabaseHas('carts', [
        'user_id' => null,
    ]);

    $cart = Cart::query()
        ->whereNull('user_id')
        ->firstOrFail();

    $this->assertDatabaseHas('cart_items', [
        'cart_id' => $cart->id,
        'inventory_id' => $inventory->id,
        'quantity' => 1,
    ]);
});

it('no permite que la cantidad supere el stock disponible', function () {
    $cardSet = CardSet::create([
        'name' => 'Set con stock limitado',
        'set_total' => 100,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Charizard de prueba',
        'card_number' => '006',
    ]);

    $inventory = Inventory::create([
        'card_id' => $card->id,
        'price' => 30000,
        'stock' => 1,
        'is_active' => true,
    ]);

    $component = Livewire::test(Catalog::class);

    $component->call('addToCart', $inventory->id);

    $component
        ->call('addToCart', $inventory->id)
        ->assertSet(
            'cartMessage',
            'No puedes agregar más unidades que el stock disponible.',
        )
        ->assertSet('cartMessageType', 'error')
        ->assertDispatched(
            'cart-notification',
            message: 'No puedes agregar más unidades que el stock disponible.',
            type: 'error',
        );

    $cart = Cart::query()
        ->whereNull('user_id')
        ->firstOrFail();

    $this->assertDatabaseHas('cart_items', [
        'cart_id' => $cart->id,
        'inventory_id' => $inventory->id,
        'quantity' => 1,
    ]);
});
