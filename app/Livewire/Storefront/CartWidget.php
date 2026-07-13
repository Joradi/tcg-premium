<?php

namespace App\Livewire\Storefront;

use App\Models\Cart;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
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

    public function removeItem(int $itemId): void
    {
        $cart = $this->getCart();

        if (! $cart) {
            return;
        }

        $cart->items()
            ->where('id', $itemId)
            ->delete();

        $this->updateCartCount();
        $this->dispatch('cart-updated');
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

    private function getCart(): ?Cart
    {
        return Cart::with(['items.inventory.card'])
            ->where('session_id', session()->getId())
            ->when(
                auth()->check(),
                fn ($query) => $query->orWhere('user_id', auth()->id()),
            )
            ->first();
    }
}
