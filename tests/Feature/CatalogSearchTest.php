<?php

use App\Livewire\Storefront\Catalog;
use Livewire\Livewire;

it('permite actualizar la búsqueda del catálogo', function () {
    Livewire::test(Catalog::class)
        ->set('search', 'Pikachu')
        ->assertSet('search', 'Pikachu');
});
