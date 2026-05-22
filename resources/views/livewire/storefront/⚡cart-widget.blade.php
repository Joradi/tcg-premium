<?php

use App\Models\Cart;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
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
        $cart = $this->getCart();
        $this->itemCount = $cart ? $cart->items()->sum('quantity') : 0;
    }

    public function removeItem($itemId)
    {
        $cart = $this->getCart();
        if ($cart) {
            $cart->items()->where('id', $itemId)->delete();
            $this->updateCartCount();
            $this->dispatch('cart-updated');
        }
    }

    private function getCart()
    {
        return Cart::with(['items.inventory.card'])
            ->where('session_id', session()->getId())
            ->orWhere(function($query) {
                if (auth()->check()) {
                    $query->where('user_id', auth()->id());
                }
            })
            ->first();
    }

    #[Computed]
    public function cartItems()
    {
        $cart = $this->getCart();
        return $cart ? $cart->items : collect();
    }

    #[Computed]
    public function cartTotal()
    {
        return $this->cartItems->sum(function($item) {
            return $item->quantity * $item->inventory->price;
        });
    }
};
?>

<div x-data="{ open: false }" class="relative">

    <div @click="open = true" class="relative cursor-pointer flex items-center justify-center p-2 text-gray-400 hover:text-white transition-colors group">
        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>

        @if($itemCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-bold text-white transform translate-x-1/4 -translate-y-1/4 bg-blue-600 rounded-full shadow-md border-2 border-gray-950">
                {{ $itemCount }}
            </span>
        @endif
    </div>

    <div x-show="open" style="display: none;" class="fixed inset-0 z-[100] overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 overflow-hidden">
            <div x-show="open"
                 x-transition:enter="ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="open = false"
                 class="absolute inset-0 bg-black/70 backdrop-blur-sm transition-opacity"></div>

            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div x-show="open"
                     x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                     class="pointer-events-auto w-screen max-w-md">

                    <div class="flex h-full flex-col bg-gray-900 shadow-2xl border-l border-gray-800">
                        <div class="flex items-start justify-between px-6 py-5 border-b border-gray-800">
                            <h2 class="text-xl font-black text-white uppercase tracking-tight" id="slide-over-title">Tu Mazo</h2>
                            <button @click="open = false" class="text-gray-400 hover:text-white transition-colors p-1">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <div class="flex-1 overflow-y-auto px-6 py-6">
                            @if($this->cartItems->isEmpty())
                                <div class="flex flex-col items-center justify-center h-full text-gray-500">
                                    <svg class="w-16 h-16 mb-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <p class="text-lg font-medium">El carrito está vacío</p>
                                </div>
                            @else
                                <ul role="list" class="-my-6 divide-y divide-gray-800">
                                    @foreach($this->cartItems as $item)
                                        <li class="flex py-6">
                                            <div class="h-24 w-16 flex-shrink-0 overflow-hidden rounded-md border border-gray-800 bg-gray-950 flex items-center justify-center">
                                                <img src="{{ $item->inventory->card->image_url }}" alt="{{ $item->inventory->card->name }}" class="h-full w-full object-contain object-center">
                                            </div>

                                            <div class="ml-4 flex flex-1 flex-col justify-center">
                                                <div>
                                                    <div class="flex justify-between text-base font-bold text-white">
                                                        <h3>{{ $item->inventory->card->name }}</h3>
                                                        <p class="ml-4 text-emerald-400">${{ number_format($item->inventory->price * $item->quantity, 0, ',', '.') }}</p>
                                                    </div>
                                                    <p class="mt-1 text-xs text-gray-500">{{ $item->inventory->language }} · {{ $item->inventory->condition }}</p>
                                                </div>
                                                <div class="flex flex-1 items-end justify-between text-sm mt-2">
                                                    <p class="text-gray-400 font-medium">Cant: {{ $item->quantity }}</p>

                                                    <button wire:click="removeItem({{ $item->id }})" type="button" class="font-medium text-red-500 hover:text-red-400 transition-colors">Eliminar</button>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <div class="border-t border-gray-800 px-6 py-6 bg-gray-950/50">
                            <div class="flex justify-between text-lg font-black text-white mb-4">
                                <p>Total</p>
                                <p class="text-emerald-400">${{ number_format($this->cartTotal, 0, ',', '.') }} CLP</p>
                            </div>

                            <button @if($this->cartItems->isEmpty()) disabled @endif class="w-full flex items-center justify-center rounded-xl border border-transparent bg-blue-600 px-6 py-4 text-base font-bold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                Proceder al Pago
                            </button>

                            <div class="mt-4 flex justify-center text-center text-xs text-gray-500">
                                <p>
                                    o <button @click="open = false" type="button" class="font-medium text-blue-400 hover:text-blue-300 transition-colors">Continuar buscando cartas<span aria-hidden="true"> &rarr;</span></button>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
