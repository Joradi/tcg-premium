<?php

namespace App\Livewire\Storefront;

use App\Models\Cart;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class CartWidget extends Component
{
    public int $itemCount = 0;

    public function mount(): void
    {
        $this->updateCartCount();
    }

    #[On('cart-updated')]
    public function updateCartCount(): void
    {
        $cart = $this->getCart();

        $this->itemCount = $cart
            ? $cart->items()->sum('quantity')
            : 0;
    }

    public function increaseQuantity(int $itemId): void
    {
        $cart = $this->getCart();

        if (! $cart) {
            return;
        }

        $quantityIncreased = DB::transaction(
            function () use ($cart, $itemId): ?bool {
                $item = $cart->items()
                    ->lockForUpdate()
                    ->find($itemId);

                if (! $item) {
                    return null;
                }

                $inventory = $item->inventory()
                    ->lockForUpdate()
                    ->first();

                if (! $inventory || $item->quantity >= $inventory->stock) {
                    return false;
                }

                $item->increment('quantity');

                return true;
            },
        );

        if ($quantityIncreased === null) {
            return;
        }

        if ($quantityIncreased === false) {
            $this->dispatch(
                'cart-notification',
                message: 'No puedes agregar más unidades que el stock disponible.',
                type: 'error'
            );

            return;
        }

        $this->refreshCartState();

        $this->dispatch(
            'cart-notification',
            message: 'Cantidad actualizada en el carrito.',
            type: 'success',
        );
    }

    public function decreaseQuantity(int $itemId): void
    {
        $cart = $this->getCart();

        if (! $cart) {
            return;
        }

        $quantityDecreased = DB::transaction(
            function () use ($cart, $itemId): ?bool {
                $item = $cart->items()
                    ->lockForUpdate()
                    ->find($itemId);

                if (! $item) {
                    return null;
                }

                if ($item->quantity <= 1) {
                    return false;
                }

                $item->decrement('quantity');

                return true;
            },
        );

        if ($quantityDecreased === null) {
            return;
        }

        if ($quantityDecreased === false) {
            $this->dispatch(
                'cart-notification',
                message: 'La cantidad mínima es 1. Usa Quitar para eliminar el producto.',
                type: 'error',
            );

            return;
        }

        $this->refreshCartState();

        $this->dispatch(
            'cart-notification',
            message: 'Cantidad actualizada en el carrito.',
            type: 'success',
        );
    }

    public function removeItem(int $itemId): void
    {
        $cart = $this->getCart();

        if (! $cart) {
            return;
        }

        $itemRemoved = DB::transaction(
            function () use ($cart, $itemId): bool {
                $item = $cart->items()
                    ->lockForUpdate()
                    ->find($itemId);

                if (! $item) {
                    return false;
                }

                $item->delete();

                return true;
            },
        );

        if (! $itemRemoved) {
            return;
        }

        $this->refreshCartState();

        $this->dispatch(
            'cart-notification',
            message: 'Producto quitado del carrito.',
            type: 'success',
        );
    }

    #[Computed]
    public function cartItems(): Collection
    {
        $cart = $this->getCart();

        return $cart
            ? $cart->items
            : collect();
    }

    #[Computed]
    public function cartTotal(): int|float
    {
        return $this->cartItems->sum(
            fn ($item) => $item->quantity * $item->inventory->price,
        );
    }

    public function render(): View
    {
        return view('livewire.storefront.cart-widget');
    }

    private function refreshCartState(): void
    {
        unset($this->cartItems, $this->cartTotal);

        $this->updateCartCount();
        $this->dispatch(
            'cart-updated'
        );
    }

    private function getCart(): ?Cart
    {
        return Cart::with(['items.inventory.card'])
            ->where('session_id', session()->getId())
            ->when(
                auth()->check(),
                fn ($query) => $query->orWhere('user_id', auth()->id())
            )
            ->first();
    }
}
