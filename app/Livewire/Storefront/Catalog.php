<?php

namespace App\Livewire\Storefront;

use App\Actions\Cart\ResolveActiveCart;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Catalog extends Component
{
    use WithPagination;

    public $search = '';

    public $selectedProduct = null;

    public ?string $cartMessage = null;

    public string $cartMessageType = 'success';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openQuickView($id)
    {
        $this->selectedProduct = Inventory::with(['card.set'])->find($id);
    }

    public function closeQuickView()
    {
        $this->selectedProduct = null;
    }

    public function render()
    {
        $query = Inventory::with(['card.set'])
            ->where('is_active', true)
            ->where('stock', '>', 0);

        if (! empty($this->search)) {
            $query->whereHas('card', function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('artist', 'like', '%'.$this->search.'%');
            });
        }

        return view('livewire.storefront.catalog', [
            'products' => $query->orderBy('created_at', 'desc')->paginate(12),
        ]);
    }

    public function addToCart(int $inventoryId): void
    {
        $cartUpdated = DB::transaction(function () use ($inventoryId): bool {
            $inventory = Inventory::query()
                ->lockForUpdate()
                ->find($inventoryId);

            if (! $inventory || $inventory->stock < 1) {
                $this->cartMessage = 'Este producto no tiene stock disponible.';
                $this->cartMessageType = 'error';

                return false;
            }

            $cart = app(ResolveActiveCart::class)->handle(
                sessionId: session()->getId(),
                userId: auth()->id(),
            );

            $cartItem = $cart->items()
                ->where('inventory_id', $inventoryId)
                ->lockForUpdate()
                ->first();

            if ($cartItem) {
                if ($cartItem->quantity >= $inventory->stock) {
                    $this->cartMessage = 'No puedes agregar más unidades que el stock disponible.';
                    $this->cartMessageType = 'error';

                    return false;
                }
                $cartItem->increment('quantity');

                $this->cartMessage = 'Cantidad actualizada en el carrito';
                $this->cartMessageType = 'success';

                return true;
            }

            $cart->items()->create([
                'inventory_id' => $inventoryId,
                'quantity' => 1,
            ]);

            $this->cartMessage = 'Producto agregado al carrito';
            $this->cartMessageType = 'success';

            return true;

        });

        $this->closeQuickView();

        $this->dispatch(
            'cart-notification',
            message: $this->cartMessage,
            type: $this->cartMessageType
        );

        if ($cartUpdated) {
            $this->dispatch('cart-updated');
        }

    }
}
