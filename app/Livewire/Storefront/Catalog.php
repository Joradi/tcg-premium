<?php

namespace App\Livewire\Storefront;

use App\Models\Inventory;
use Livewire\Component;
use Livewire\WithPagination;

class Catalog extends Component
{
    use WithPagination;

    public $search = '';

    // 1. La variable que guarda la carta seleccionada para el Modal
    public $selectedProduct = null;

    protected function updatingSearch()
    {
        $this->resetPage();
    }

    // 2. Función que se dispara al hacer clic en la tarjeta
    public function openQuickView($id)
    {
        $this->selectedProduct = Inventory::with(['card.set'])->find($id);
    }

    // 3. Función que se dispara al hacer clic en la "X" o fuera del modal
    public function closeQuickView()
    {
        $this->selectedProduct = null;
    }

    public function render()
    {
        // Solo mostramos cartas activas y con stock
        $query = Inventory::with(['card.set'])
            ->where('is_active', true)
            ->where('stock', '>', 0);

        // 4. El buscador público (ahora buscando por artista también)
        if (!empty($this->search)) {
            $query->whereHas('card', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('artist', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.storefront.catalog', [
            'products' => $query->orderBy('created_at', 'desc')->paginate(12),
        ]);
    }
}
