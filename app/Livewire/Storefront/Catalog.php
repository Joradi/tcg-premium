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
        $query = Inventory::with(['card.set'])
            ->where('is_active', true)
            ->where('stock', '>', 0);

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

    public function addToCart($inventoryId)
    {

        \Illuminate\Support\Facades\DB::transaction(function () use ($inventoryId) {

            $inventory = \App\Models\Inventory::where('id', $inventoryId)
                ->lockForUpdate()
                ->first();

            if (!$inventory || $inventory->stock < 1) {
                return;
            }

            $cart = \App\Models\Cart::firstOrCreate([
                'session_id' => session()->getId(),
                'user_id' => auth()->id(),
            ]);

            $cartItem = $cart->items()->where('inventory_id', $inventoryId)->first();

            if ($cartItem) {
                if ($inventory->stock > $cartItem->quantity) {
                    $cartItem->increment('quantity');
                }
            } else {
                $cart->items()->create([
                    'inventory_id' => $inventoryId,
                    'quantity' => 1
                ]);
            }
        });

        $this->closeQuickView();

        $this->dispatch('cart-updated');
    }
}
