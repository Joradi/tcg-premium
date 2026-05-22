<?php

use App\Models\Cart;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public $itemCount = 0;

    public function mount()
    {
        $this->updateCartCount();
    }

    #[On('cart-updated')]
    public function updateCartCount()
    {
        $cart = Cart::withSum('items', 'quantity')
            ->where('session_id', session()->getId())
            ->orWhere(function($query) {
                if (auth()->check()) {
                    $query->where('user_id', auth()->id());
                }
            })
            ->first();

        $this->itemCount = $cart ? $cart->items_sum_quantity : 0;
    }
};
?>

<div class="relative cursor-pointer flex items-center justify-center p-2 text-gray-400 hover:text-white transition-colors group">
    <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>

    @if($itemCount > 0)
        <span class="absolute top-0 right-0 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-bold text-white transform translate-x-1/4 -translate-y-1/4 bg-blue-600 rounded-full shadow-md border-2 border-gray-950">
            {{ $itemCount }}
        </span>
    @endif
</div>

