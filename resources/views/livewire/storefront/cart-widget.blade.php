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
                 class="absolute inset-0 bg-[#12001F]/85 backdrop-blur-sm transition-opacity"></div>

            <div class="pointer-events-none fixed inset-y-0 right-0 flex w-full justify-end sm:pl-10">
                <div x-show="open"
                     x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                     class="pointer-events-auto h-[100dvh] w-full bg-[#12001F] sm:max-w-lg">

                    <div class="grid h-full min-h-0 grid-rows-[auto_minmax(0,1fr)_auto] overflow-hidden border-l border-[#7B2CBF]/30 bg-[#12001F] shadow-2xl shadow-black/50">
                        <div class="flex shrink-0 items-start justify-between border-b border-gray-800 px-6 py-5">
                            <h2 class="text-xl font-black uppercase tracking-tight text-[#FFF8E7]" id="slide-over-title">Tu Mazo</h2>
                            <button @click="open = false" class="p-1 text-[#FFF8E7]/55 transition-colors hover:text-[#80FFDB]">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <div class="min-h-0 overflow-y-auto px-4 py-4 sm:px-6 sm:py-6">
                            @if($this->cartItems->isEmpty())
                                <div class="flex flex-col items-center justify-center h-full text-gray-500">
                                    <svg class="w-16 h-16 mb-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <p class="text-lg font-medium">El carrito está vacío</p>
                                </div>
                            @else
                                <ul role="list" class="space-y-4">
                                    @foreach($this->cartItems as $item)
                                        @include('livewire.storefront.partials.cart-item', [
                                            'item' => $item,
                                        ])
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <div class="shrink-0 border-t border-[#7B2CBF]/25 bg-[#2B2D42]/45 px-4 py-4 sm:px-6 sm:py-6">
                            <div class="mb-4 flex items-baseline justify-between gap-4 text-lg font-black text-[#FFF8E7]">
                                <p class="shrink-0">Total</p>

                                <p class="min-w-0 whitespace-nowrap text-right tabular-nums text-[#80FFDB]">
                                    ${{ number_format($this->cartTotal, 0, ',', '.') }}
                                </p>
                            </div>

                            @if($this->cartItems->isEmpty())
                                <button
                                    type="button"
                                    disabled
                                    class="flex w-full cursor-not-allowed items-center justify-center rounded-xl border border-[#7B2CBF] bg-[#7B2CBF] px-6 py-4 text-base font-bold text-[#FFF8E7] opacity-50"
                                >
                                    Proceder al Pago
                                </button>
                            @else
                                <a
                                    href="{{ route('storefront.checkout') }}"
                                    class="flex w-full items-center justify-center rounded-xl border border-[#7B2CBF] bg-[#7B2CBF] px-6 py-4 text-base font-bold text-[#FFF8E7] shadow-lg shadow-[#7B2CBF]/20 transition-colors hover:bg-[#5A189A]"
                                >
                                    Proceder al Pago
                                </a>
                            @endif

                            <p class="mt-3 text-center text-[11px] text-[#FFF8E7]/40">
                                Precios expresados en pesos chilenos
                            </p>

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
