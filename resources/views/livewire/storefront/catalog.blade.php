<div class="relative min-h-screen bg-[#12001F] px-4 py-12 text-[#FFF8E7] sm:px-6 lg:px-8">
    <div
        x-data="{
        visible: false,
        message: '',
        type: 'success',
        timeout: null
    }"
        x-on:cart-notification.window="
        clearTimeout(timeout);

        message = $event.detail.message;
        type = $event.detail.type;
        visible = true;

        timeout = setTimeout(() => {
            visible = false;
        }, 3500);
    "
        x-show="visible"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        :class="type === 'success'
        ? 'border-emerald-500/40 bg-emerald-950/95 text-emerald-200'
        : 'border-red-500/40 bg-red-950/95 text-red-200'"
        :role="type === 'error' ? 'alert' : 'status'"
        class="fixed left-1/2 top-20 z-[100] w-[calc(100%-2rem)] max-w-md -translate-x-1/2 rounded-xl border px-4 py-3 shadow-2xl backdrop-blur"
        style="display: none;"
    >
        <div class="flex items-start gap-3">
        <span
            class="mt-0.5 font-bold"
            x-text="type === 'success' ? '✓' : '!'"
        ></span>

            <p class="flex-1 text-sm font-medium" x-text="message"></p>

            <button
                type="button"
                class="opacity-70 transition hover:opacity-100"
                x-on:click="visible = false"
                aria-label="Cerrar notificación"
            >
                &times;
            </button>
        </div>
    </div>
    <div class="max-w-7xl mx-auto">

        <div class="mb-10 flex flex-col items-center justify-between gap-6 rounded-3xl border border-[#7B2CBF]/20 bg-[#2B2D42]/30 px-6 py-7 text-center shadow-[0_20px_60px_rgba(0,0,0,0.16)] backdrop-blur md:flex-row md:text-left lg:px-8">
            <div>
                <h1 class="text-4xl font-black text-white tracking-tight">Catálogo de Singles</h1>
                <p class="text-gray-400 mt-2 text-lg">Encuentra las cartas que faltan para tu mazo competitivo.</p>
            </div>

            <div class="w-full md:max-w-md">
                <input
                    wire:model.live.debounce.300ms="search"
                    type="search"
                    placeholder="Buscar por Pokémon o Entrenador..."
                    class="w-full rounded-xl border border-[#7B2CBF]/30 bg-[#2B2D42]/55 px-5 py-3 text-[#FFF8E7] shadow-inner outline-none transition placeholder:text-[#FFF8E7]/35 focus:border-[#80FFDB]/60 focus:ring-4 focus:ring-[#80FFDB]/10"
                />
            </div>
        </div>

        <div class="grid grid-cols-[repeat(auto-fit,minmax(240px,280px))] justify-center gap-6 xl:gap-8">
            @forelse($products as $product)
                <div
                    wire:click="openQuickView({{ $product->id }})"
                    class="group flex cursor-pointer flex-col rounded-2xl border border-[#7B2CBF]/25 bg-[#2B2D42]/70 p-4 shadow-[0_18px_50px_rgba(0,0,0,0.22)] transition-all duration-300 hover:-translate-y-1 hover:border-[#80FFDB]/35 hover:shadow-[0_24px_70px_rgba(90,24,154,0.22)]"
                >

                    <div class="relative overflow-hidden rounded-xl mb-4 flex justify-center bg-gray-950 p-2">
                        <img src="{{ $product->card->image_url }}" alt="{{ $product->card->name }}" class="w-48 h-64 object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-xl">
                        <div class="absolute right-2 top-2 rounded-lg border border-[#7B2CBF]/40 bg-[#12001F]/90 px-2.5 py-1 text-xs font-bold text-[#80FFDB] shadow-lg backdrop-blur-sm">
                            {{ $product->condition }}
                        </div>
                    </div>

                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-white leading-tight mb-1">{{ $product->card->name }}</h3>
                        <p class="text-xs text-gray-500 mb-3">{{ $product->card->set->name ?? 'Sin Set' }}</p>

                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="px-2 py-1 bg-gray-800 text-gray-300 rounded text-[10px] uppercase font-bold tracking-wider">{{ $product->language }}</span>
                            <span class="rounded-lg border border-[#7B2CBF]/40 bg-[#7B2CBF]/15 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-[#FFF8E7]/75">
                                {{ $product->variant }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-auto pt-4 border-t border-gray-800 flex items-center justify-between">
                        <div class="text-2xl font-black text-emerald-400">${{ number_format($product->price, 0, ',', '.') }}</div>
                        <button
                            type="button"
                            wire:click.stop="addToCart({{ $product->id }})"
                            wire:loading.attr="disabled"
                            wire:target="addToCart"
                            class="rounded-xl border border-[#7B2CBF] bg-[#7B2CBF] p-2.5 text-[#FFF8E7] shadow-lg shadow-[#7B2CBF]/20 transition-all hover:bg-[#5A189A] active:scale-95 disabled:cursor-not-allowed disabled:opacity-50"
                            title="Agregar al carrito"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                                ></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-20 bg-gray-900 border border-gray-800 rounded-3xl">
                    <svg class="h-16 w-16 text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <h3 class="text-xl font-medium text-white">No hay cartas en venta por ahora</h3>
                    <p class="text-gray-500 mt-2">Estamos reabasteciendo el stock.</p>
                </div>
            @endforelse
        </div>

        @if($products->hasPages())
            <div class="mt-10 p-4 bg-gray-900 border border-gray-800 rounded-2xl">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    @if($selectedProduct)
        <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto p-4 sm:p-6 md:items-center">
            <div
                class="fixed inset-0 bg-[#12001F]/90 backdrop-blur-md transition-opacity"
                wire:click="closeQuickView"
            ></div>

            <div class="relative z-50 flex w-full max-w-4xl flex-col overflow-hidden rounded-3xl border border-[#7B2CBF]/30 bg-[#2B2D42] shadow-[0_30px_100px_rgba(0,0,0,0.55)] md:flex-row">

                <button
                    type="button"
                    wire:click="closeQuickView"
                    class="absolute right-4 top-4 z-10 rounded-full border border-[#7B2CBF]/25 bg-[#12001F]/80 p-2 text-[#FFF8E7]/55 transition hover:border-[#80FFDB]/40 hover:text-[#80FFDB]"
                    aria-label="Cerrar detalle del producto"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <div class="flex w-full items-center justify-center border-b border-[#7B2CBF]/20 bg-[#12001F]/70 p-6 md:w-2/5 md:border-b-0 md:border-r md:p-8">
                    <img src="{{ $selectedProduct->card->image_url }}" alt="{{ $selectedProduct->card->name }}" class="w-full max-h-[450px] rounded-lg shadow-lg shadow-black/50 object-contain transition-transform duration-300">
                </div>

                <div class="w-full md:w-3/5 p-8 flex flex-col justify-center">

                    <h2 class="text-3xl font-bold text-white tracking-tight">{{ $selectedProduct->card->name }}</h2>
                    <p class="text-gray-400 mt-1 text-sm font-medium">
                        {{ $selectedProduct->card->set->name ?? 'Sin Set' }}
                        <span class="text-gray-500">· #{{ $selectedProduct->card->card_number }}/{{ $selectedProduct->card->set->set_total ?? '?' }}</span>
                    </p>

                    <div class="h-px bg-gray-800 w-full my-5"></div>

                    <div class="space-y-2 mb-6 text-sm">
                        <div class="flex justify-between gap-4">
                            <span class="text-gray-500">Rareza</span>
                            <span class="text-gray-200 font-medium text-right">{{ $selectedProduct->card->rarity ?? 'Desconocida' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-gray-500">Artista</span>
                            <span class="text-gray-200 font-medium text-right">{{ $selectedProduct->card->artist ?? 'Desconocido' }}</span>
                        </div>
                        @if($selectedProduct->card->card_type)
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Tipo</span>
                                <span class="text-blue-400 font-semibold text-right">{{ $selectedProduct->card->card_type }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="mb-6 rounded-2xl border border-[#7B2CBF]/25 bg-[#12001F]/70 p-5 shadow-inner">
                        <div class="mb-4 flex items-end justify-between gap-4">
                            <div>
                                <p class="mb-1 text-xs font-bold uppercase tracking-wider text-[#FFF8E7]/45">
                                    Precio
                                </p>

                                <span class="text-3xl font-black tabular-nums text-[#80FFDB]">
                ${{ number_format($selectedProduct->price, 0, ',', '.') }}
            </span>
                            </div>

                            <span class="text-right text-[11px] text-[#FFF8E7]/35">
            Pesos chilenos
        </span>
                        </div>

                        <div class="flex flex-wrap gap-2">
        <span class="rounded-lg bg-[#2B2D42] px-2.5 py-1 text-xs font-semibold text-[#FFF8E7]/75">
            {{ $selectedProduct->language }}
        </span>

                            <span class="rounded-lg border border-[#80FFDB]/20 bg-[#80FFDB]/10 px-2.5 py-1 text-xs font-semibold text-[#80FFDB]">
            {{ $selectedProduct->condition }}
        </span>

                            <span class="rounded-lg border border-[#7B2CBF]/40 bg-[#7B2CBF]/15 px-2.5 py-1 text-xs font-semibold text-[#FFF8E7]/75">
            {{ $selectedProduct->variant }}
        </span>
                        </div>
                    </div>

                    <button
                        type="button"
                        wire:click="addToCart({{ $selectedProduct->id }})"
                        wire:loading.attr="disabled"
                        wire:target="addToCart"
                        class="flex w-full items-center justify-center gap-2 rounded-xl border border-[#7B2CBF] bg-[#7B2CBF]
                        px-4 py-3.5 text-sm font-bold uppercase tracking-wider text-[#FFF8E7] shadow-lg shadow-[#7B2CBF]/20
                        transition-all hover:bg-[#5A189A] active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293
                                   2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4
                                   2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                            ></path>
                        </svg>

                        <span wire:loading.remove wire:target="addToCart">
                        Agregar al carrito
                        </span>

                        <span wire:loading wire:target="addToCart">
                         Agregando...
                        </span>
                    </button>

                    <p class="text-center text-xs text-gray-500 mt-3.5 font-medium">
                        Solo quedan {{ $selectedProduct->stock }} unidades en inventario
                    </p>

                </div>
            </div>
        </div>
    @endif
</div>
