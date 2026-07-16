<?php

use App\Actions\Cart\ResolveActiveCart;
use App\Models\Card;
use App\Models\CardSet;
use App\Models\Cart;
use App\Models\Inventory;
use App\Models\User;

it('asocia el carrito invitado al usuario cuando inicia sesión', function () {
    $user = User::factory()->create();

    $guestCart = Cart::create([
        'user_id' => null,
        'session_id' => 'guest-session-123',
    ]);

    $resolvedCart = app(ResolveActiveCart::class)->handle(
        sessionId: 'guest-session-123',
        userId: $user->id,
        createIfMissing: false,
    );

    expect($resolvedCart)->not->toBeNull()
        ->and($resolvedCart->is($guestCart))->toBeTrue()
        ->and($resolvedCart->user_id)->toBe($user->id)
        ->and($resolvedCart->session_id)->toBe('guest-session-123');

    $this->assertDatabaseCount('carts', 1);

    $this->assertDatabaseHas('carts', [
        'id' => $guestCart->id,
        'user_id' => $user->id,
        'session_id' => 'guest-session-123',
    ]);
});

it('recupera el carrito del usuario cuando cambia la sesión', function () {
    $user = User::factory()->create();

    $userCart = Cart::create([
        'user_id' => $user->id,
        'session_id' => 'old-session-123',
    ]);

    $resolvedCart = app(ResolveActiveCart::class)->handle(
        sessionId: 'new-session-456',
        userId: $user->id,
        createIfMissing: false,
    );

    expect($resolvedCart)->not->toBeNull()
        ->and($resolvedCart->is($userCart))->toBeTrue()
        ->and($resolvedCart->user_id)->toBe($user->id)
        ->and($resolvedCart->session_id)->toBe('new-session-456');

    $this->assertDatabaseCount('carts', 1);

    $this->assertDatabaseHas('carts', [
        'id' => $userCart->id,
        'user_id' => $user->id,
        'session_id' => 'new-session-456',
    ]);
});

it('evita dejar dos carritos cuando el usuario ya tenía uno', function () {
    $user = User::factory()->create();

    $userCart = Cart::create([
        'user_id' => $user->id,
        'session_id' => 'old-user-session',
    ]);

    $guestCart = Cart::create([
        'user_id' => null,
        'session_id' => 'current-guest-session',
    ]);

    $resolvedCart = app(ResolveActiveCart::class)->handle(
        sessionId: 'current-guest-session',
        userId: $user->id,
        createIfMissing: false,
    );

    expect($resolvedCart)->not->toBeNull()
        ->and($resolvedCart->is($userCart))->toBeTrue()
        ->and($resolvedCart->user_id)->toBe($user->id)
        ->and($resolvedCart->session_id)->toBe('current-guest-session');

    $this->assertDatabaseCount('carts', 1);

    $this->assertDatabaseMissing('carts', [
        'id' => $guestCart->id,
    ]);
});

it('combina los productos de los carritos sin perder líneas', function () {
    $user = User::factory()->create();

    $cardSet = CardSet::create([
        'name' => 'Set combinación de carritos',
        'set_total' => 20,
    ]);

    $firstCard = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Carta compartida',
        'card_number' => '001',
    ]);

    $secondCard = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Carta invitada',
        'card_number' => '002',
    ]);

    $firstInventory = Inventory::create([
        'card_id' => $firstCard->id,
        'language' => 'Español',
        'condition' => 'Near Mint (NM)',
        'variant' => 'Normal',
        'price' => 11900,
        'stock' => 10,
        'is_active' => true,
    ]);

    $secondInventory = Inventory::create([
        'card_id' => $secondCard->id,
        'language' => 'Inglés',
        'condition' => 'Excellent (EX)',
        'variant' => 'Holo',
        'price' => 5950,
        'stock' => 5,
        'is_active' => true,
    ]);

    $userCart = Cart::create([
        'user_id' => $user->id,
        'session_id' => 'old-user-session',
    ]);

    $userCart->items()->create([
        'inventory_id' => $firstInventory->id,
        'quantity' => 1,
    ]);

    $guestCart = Cart::create([
        'user_id' => null,
        'session_id' => 'current-guest-session',
    ]);

    $guestCart->items()->createMany([
        [
            'inventory_id' => $firstInventory->id,
            'quantity' => 2,
        ],
        [
            'inventory_id' => $secondInventory->id,
            'quantity' => 1,
        ],
    ]);

    $resolvedCart = app(ResolveActiveCart::class)->handle(
        sessionId: 'current-guest-session',
        userId: $user->id,
        createIfMissing: false,
    );

    $items = $resolvedCart->items()
        ->get()
        ->keyBy('inventory_id');

    expect($resolvedCart->is($userCart))->toBeTrue()
        ->and($items)->toHaveCount(2)
        ->and($items[$firstInventory->id]->quantity)->toBe(3)
        ->and($items[$secondInventory->id]->quantity)->toBe(1);

    $this->assertDatabaseMissing('carts', [
        'id' => $guestCart->id,
    ]);
});
