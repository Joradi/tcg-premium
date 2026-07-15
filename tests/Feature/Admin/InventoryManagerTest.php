<?php

use App\Livewire\Admin\InventoryManager;
use App\Models\Card;
use App\Models\CardSet;
use App\Models\Inventory;
use Livewire\Livewire;

it('filtra el inventario al buscar por nombre de carta', function() {
    $cardSet = CardSet::create([
        'name' => 'Set de prueba',
        'set_total' => 100,
    ]);

    $pikachu = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Pikachu de prueba',
        'card_number' => '025',
    ]);

    $charizard = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Charizard de prueba',
        'card_number' => '006',
    ]);

    Inventory::create([
        'card_id' => $pikachu->id,
        'price' => 15000,
        'stock' => 3,
        'is_active' => true,
        ]);

    Inventory::create([
        'card_id' => $charizard->id,
        'price' => 30000,
        'stock' => 2,
        'is_active' => true,
        ]);
    Livewire::test(InventoryManager::class)
        ->set('search', 'Pikachu')
        ->assertSee('Pikachu de prueba')
        ->assertDontSee('Charizard de prueba');
});

it('carga la carta seleccionada al editar un producto', function() {
    $cardSet = CardSet::create([
        'name' => 'Set para edición',
        'set_total' => 120,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Lucario de prueba',
        'card_number' => '079',
        ]);

    $inventory = Inventory::create([
        'card_id' => $card->id,
        'language' => 'Japonés',
        'condition' => 'Lightly Played (LP)',
        'variant' => 'Holo',
        'price' => 18500,
        'stock' => 4,
        'is_active' => true,
        ]);

    Livewire::test(InventoryManager::class)
        ->set('cardSearch', 'Búsqueda anterior')
        ->call('edit', $inventory->id)
        ->assertSet('inventoryId', $inventory->id)
        ->assertSet('card_id', $card->id)
        ->assertSet('selectedCard.id', $card->id)
        ->assertSet('cardSearch', '')
        ->assertSet('language', 'Japonés')
        ->assertSet('condition', 'Lightly Played (LP)')
        ->assertSet('variant', 'Holo')
        ->assertSet('price', 18500)
        ->assertSet('stock', 4)
        ->assertSet('is_active', true)
        ->assertSet('isOpen', true)
        ->assertSee('Lucario de prueba');

});

it('guarda la variante seleccionada al publicar un producto', function () {
    $cardSet = CardSet::create([
        'name' => 'Set para publicación',
        'set_total' => 150,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Gengar de prueba',
        'card_number' => '094',
    ]);

    Livewire::test(InventoryManager::class)
        ->call('create')
        ->set('card_id', $card->id)
        ->set('language', 'Japonés')
        ->set('condition', 'Near Mint (NM)')
        ->set('variant', 'Holo')
        ->set('price', 22000)
        ->set('stock', 3)
        ->set('is_active', true)
        ->call('store')
        ->assertHasNoErrors()
        ->assertSet('isOpen', false);

    $this->assertDatabaseHas('inventories', [
        'card_id' => $card->id,
        'language' => 'Japonés',
        'condition' => 'Near Mint (NM)',
        'variant' => 'Holo',
        'price' => 22000,
        'stock' => 3,
        'is_active' => true,
    ]);
});

it('actualiza precio stock y variante de un producto existente', function () {
    $cardSet = CardSet::create([
        'name' => 'Set para actualización',
        'set_total' => 180,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Mewtwo de prueba',
        'card_number' => '150',
    ]);

    $inventory = Inventory::create([
        'card_id' => $card->id,
        'language' => 'Español',
        'condition' => 'Near Mint (NM)',
        'variant' => 'Normal',
        'price' => 12000,
        'stock' => 2,
        'is_active' => true,
    ]);

    Livewire::test(InventoryManager::class)
        ->call('edit', $inventory->id)
        ->set('condition', 'Lightly Played (LP)')
        ->set('variant', 'Reverse Holo')
        ->set('price', 19500)
        ->set('stock', 8)
        ->call('store')
        ->assertHasNoErrors()
        ->assertSet('isOpen', false);

    $this->assertDatabaseHas('inventories', [
        'id' => $inventory->id,
        'card_id' => $card->id,
        'condition' => 'Lightly Played (LP)',
        'variant' => 'Reverse Holo',
        'price' => 19500,
        'stock' => 8,
        'is_active' => true,
    ]);

    $this->assertDatabaseCount('inventories', 1);
});

it('cambia el estado activo de un producto', function () {
    $cardSet = CardSet::create([
        'name' => 'Set para visibilidad',
        'set_total' => 200,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Umbreon de prueba',
        'card_number' => '197',
    ]);

    $inventory = Inventory::create([
        'card_id' => $card->id,
        'language' => 'Español',
        'condition' => 'Near Mint (NM)',
        'variant' => 'Normal',
        'price' => 25000,
        'stock' => 5,
        'is_active' => true,
    ]);

    $component = Livewire::test(InventoryManager::class);

    $component->call('toggleActive', $inventory->id);

    $this->assertDatabaseHas('inventories', [
        'id' => $inventory->id,
        'is_active' => false,
    ]);

    $component->call('toggleActive', $inventory->id);

    $this->assertDatabaseHas('inventories', [
        'id' => $inventory->id,
        'is_active' => true,
    ]);
});

it('rechaza valores inválidos al guardar un producto', function () {
    $cardSet = CardSet::create([
        'name' => 'Set para validación',
        'set_total' => 100,
    ]);

    $card = Card::create([
        'card_set_id' => $cardSet->id,
        'name' => 'Carta para validación',
        'card_number' => '001',
    ]);

    Livewire::test(InventoryManager::class)
        ->call('create')
        ->set('card_id', $card->id)
        ->set('language', 'Klingon')
        ->set('condition', 'Destruida')
        ->set('variant', 'Edición inexistente')
        ->set('price', -1000)
        ->set('stock', -2)
        ->call('store')
        ->assertHasErrors([
            'language',
            'condition',
            'variant',
            'price',
            'stock',
        ]);

    $this->assertDatabaseCount('inventories', 0);
});

it('rechaza una carta que no existe al guardar un producto', function () {
    Livewire::test(InventoryManager::class)
        ->call('create')
        ->set('card_id', 999999)
        ->set('language', 'Español')
        ->set('condition', 'Near Mint (NM)')
        ->set('variant', 'Normal')
        ->set('price', 10000)
        ->set('stock', 1)
        ->call('store')
        ->assertHasErrors([
            'card_id' => 'exists',
        ]);

    $this->assertDatabaseCount('inventories', 0);
});
