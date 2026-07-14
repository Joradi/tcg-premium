<?php

use App\Livewire\Storefront\CartWidget;
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

it('aumenta la cantidad de un producto sin superar el stock', function () {
    $cardSet = CardSet::create([
        'name' => 'Set para controles de cantidad',
        'set_total' => 100,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Gengar de prueba',
        'card_number' => '094',
    ]);

    $inventory = Inventory::create([
        'card_id' => $card->id,
        'price' => 20000,
        'stock' => 3,
        'is_active' => true,
    ]);

    $cart = Cart::create([
        'session_id' => session()->getId(),
        'user_id' => null,
    ]);

    $item = $cart->items()->create([
        'inventory_id' => $inventory->id,
        'quantity' => 1,
    ]);

    Livewire::test(CartWidget::class)
        ->assertSet('itemCount', 1)
        ->call('increaseQuantity', $item->id)
        ->assertSet('itemCount', 2)
        ->assertDispatched('cart-updated')
        ->assertDispatched(
            'cart-notification',
            message: 'Cantidad actualizada en el carrito.',
            type: 'success',
        );

    $this->assertDatabaseHas('cart_items', [
        'id' => $item->id,
        'cart_id' => $cart->id,
        'inventory_id' => $inventory->id,
        'quantity' => 2,
    ]);
});

it('impide aumentar la cantidad del carrito por encima del stock', function () {
    $cardSet = CardSet::create([
        'name' => 'Set con límite de cantidad',
        'set_total' => 100,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Mimikyu de prueba',
        'card_number' => '097',
    ]);

    $inventory = Inventory::create([
        'card_id' => $card->id,
        'price' => 18000,
        'stock' => 1,
        'is_active' => true,
    ]);

    $cart = Cart::create([
        'session_id' => session()->getId(),
        'user_id' => null,
    ]);

    $item = $cart->items()->create([
        'inventory_id' => $inventory->id,
        'quantity' => 1,
    ]);

    Livewire::test(CartWidget::class)
        ->assertSet('itemCount', 1)
        ->call('increaseQuantity', $item->id)
        ->assertSet('itemCount', 1)
        ->assertDispatched(
            'cart-notification',
            message: 'No puedes agregar más unidades que el stock disponible.',
            type: 'error',
        )
        ->assertNotDispatched('cart-updated');

    $this->assertDatabaseHas('cart_items', [
        'id' => $item->id,
        'quantity' => 1,
    ]);
});

it('disminuye la cantidad de un producto hasta un mínimo de uno', function () {
    $cardSet = CardSet::create([
        'name' => 'Set para disminuir cantidad',
        'set_total' => 100,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Haunter de prueba',
        'card_number' => '093',
    ]);

    $inventory = Inventory::create([
        'card_id' => $card->id,
        'price' => 16000,
        'stock' => 4,
        'is_active' => true,
    ]);

    $cart = Cart::create([
        'session_id' => session()->getId(),
        'user_id' => null,
    ]);

    $item = $cart->items()->create([
        'inventory_id' => $inventory->id,
        'quantity' => 2,
    ]);

    Livewire::test(CartWidget::class)
        ->assertSet('itemCount', 2)
        ->call('decreaseQuantity', $item->id)
        ->assertSet('itemCount', 1)
        ->assertDispatched('cart-updated')
        ->assertDispatched(
            'cart-notification',
            message: 'Cantidad actualizada en el carrito.',
            type: 'success',
        );

    $this->assertDatabaseHas('cart_items', [
        'id' => $item->id,
        'cart_id' => $cart->id,
        'inventory_id' => $inventory->id,
        'quantity' => 1,
    ]);

});

it('impide disminuir la cantidad por debajo de uno', function () {
    $cardSet = CardSet::create([
        'name' => 'Set con cantidad mínima',
        'set_total' => 1,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Gastly de prueba',
        'card_number' => '092',
    ]);

    $inventory = Inventory::create([
        'card_id' => $card->id,
        'price' => 12000,
        'stock' => 1,
        'is_active' => true,
    ]);

    $cart = Cart::create([
        'session_id' => session()->getId(),
        'user_id' => null,
    ]);

    $item = $cart->items()->create([
        'inventory_id' => $inventory->id,
        'quantity' => 1,
    ]);

    Livewire::test(CartWidget::class)
        ->assertSet('itemCount', 1)
        ->call('decreaseQuantity', $item->id)
        ->assertSet('itemCount', 1)
        ->assertDispatched(
            'cart-notification',
            message: 'La cantidad mínima es 1. Usa Quitar para eliminar el producto.',
            type: 'error',
        )
        ->assertNotDispatched('cart-updated');

    $this->assertDatabaseHas('cart_items', [
        'id' => $item->id,
        'quantity' => 1,
    ]);
});

it('quita completamente un producto del carrito', function () {
    $cardSet = CardSet::create([
        'name' => 'Set para quitar productos',
        'set_total' => 100,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Banette de prueba',
        'card_number' => '101',
    ]);

    $inventory = Inventory::create([
        'card_id' => $card->id,
        'price' => 14000,
        'stock' => 5,
        'is_active' => true,
    ]);

    $cart = Cart::create([
        'session_id' => session()->getId(),
        'user_id' => null,
    ]);

    $item = $cart->items()->create([
        'inventory_id' => $inventory->id,
        'quantity' => 3,
    ]);

    Livewire::test(CartWidget::class)
        ->assertSet('itemCount', 3)
        ->call('removeItem', $item->id)
        ->assertSet('itemCount', 0)
        ->assertDispatched('cart-updated')
        ->assertDispatched(
            'cart-notification',
            message: 'Producto quitado del carrito.',
            type: 'success',
        );

    $this->assertDatabaseMissing('cart_items', [
        'id' => $item->id,
    ]);
});
