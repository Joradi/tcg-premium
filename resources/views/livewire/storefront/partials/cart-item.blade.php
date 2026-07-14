<li
    wire:key="cart-item-{{ $item->id }}"
    class="rounded-2xl border border-[#7B2CBF]/25 bg-[#2B2D42]/70 p-4 shadow-[0_16px_40px_rgba(18,0,31,0.28)]"
>
    <div class="flex gap-4">
        <div class="flex h-28 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-[#7B2CBF]/30 bg-[#12001F]">
            <img
                src="{{ $item->inventory->card->image_url }}"
                alt="{{ $item->inventory->card->name }}"
                class="h-full w-full object-contain"
            >
        </div>

        <div class="min-w-0 flex-1">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="truncate text-base font-bold text-[#FFF8E7]">
                        {{ $item->inventory->card->name }}
                    </h3>

                    <p class="mt-1 text-xs text-[#FFF8E7]/55">
                        {{ $item->inventory->language ?? 'Idioma no especificado' }}
                        <span class="mx-1 text-[#7B2CBF]">•</span>
                        {{ $item->inventory->condition ?? 'Condición no especificada' }}
                    </p>
                </div>

                <p class="shrink-0 text-sm font-black text-[#80FFDB]">
                    ${{ number_format($item->inventory->price * $item->quantity, 0, ',', '.') }}
                </p>
            </div>

            <p class="mt-3 text-xs font-medium text-[#80FFDB]/75">
                Stock disponible: {{ $item->inventory->stock }}
            </p>

            <div class="mt-4 flex flex-wrap items-center gap-3">
                <div class="inline-flex items-center rounded-xl border border-[#7B2CBF]/40 bg-[#12001F] p-1">
                    <button
                        type="button"
                        wire:click="decreaseQuantity({{ $item->id }})"
                        wire:loading.attr="disabled"
                        @disabled($item->quantity <= 1)
                        aria-label="Disminuir cantidad de {{ $item->inventory->card->name }}"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-lg font-bold text-[#FFF8E7] transition hover:bg-[#5A189A] disabled:cursor-not-allowed disabled:opacity-30"
                    >
                        −
                    </button>

                    <span class="min-w-10 px-2 text-center text-sm font-black text-[#FFF8E7]">
                        {{ $item->quantity }}
                    </span>

                    <button
                        type="button"
                        wire:click="increaseQuantity({{ $item->id }})"
                        wire:loading.attr="disabled"
                        @disabled($item->quantity >= $item->inventory->stock)
                        aria-label="Aumentar cantidad de {{ $item->inventory->card->name }}"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-lg font-bold text-[#FFF8E7] transition hover:bg-[#5A189A] disabled:cursor-not-allowed disabled:opacity-30"
                    >
                        +
                    </button>
                </div>

                <button
                    type="button"
                    wire:click="removeItem({{ $item->id }})"
                    wire:loading.attr="disabled"
                    class="rounded-lg px-2 py-2 text-xs font-bold uppercase tracking-wider text-[#FFF8E7]/55 transition hover:bg-[#80FFDB]/10 hover:text-[#80FFDB] disabled:opacity-40"
                >
                    Quitar
                </button>
            </div>
        </div>
    </div>
</li>
