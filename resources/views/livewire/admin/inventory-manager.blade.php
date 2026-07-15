<div class="min-h-screen bg-[#12001F] px-4 py-8 text-[#FFF8E7] sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="mb-8 flex flex-col items-start justify-between gap-5 md:flex-row md:items-center">
            <div>
                <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-[#80FFDB]/70">
                    Administración
                </p>

                <h1 class="text-3xl font-black tracking-tight text-[#FFF8E7]">
                    Mis productos en venta
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-6 text-[#FFF8E7]/55">
                    Gestiona el stock, precios, variantes y visibilidad de tus cartas en tiempo real.
                </p>
            </div>

            <button
                type="button"
                wire:click="create()"
                class="inline-flex w-full items-center justify-center rounded-xl border border-[#7B2CBF] bg-[#7B2CBF] px-5 py-3 text-sm font-bold text-[#FFF8E7] shadow-lg shadow-[#7B2CBF]/20 transition-all hover:bg-[#5A189A] active:scale-[0.98] sm:w-auto"
            >
                Publicar producto
            </button>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 rounded-xl border border-[#80FFDB]/25 bg-[#80FFDB]/10 px-4 py-3 text-sm text-[#80FFDB]">
                {{ session('message') }}
            </div>
        @endif

        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="relative sm:col-span-2">
                <input
                    wire:model.live="search"
                    type="search"
                    placeholder="Busca por nombre de producto..."
                    class="w-full rounded-xl border border-[#7B2CBF]/30 bg-[#12001F]/80 px-4 py-2.5 text-sm text-[#FFF8E7] outline-none transition focus:border-[#80FFDB]/60 focus:ring-2 focus:ring-[#80FFDB]/15"
                >
            </div>

            <div>
                <select
                    wire:model.live="filterCondition"
                    class="w-full rounded-xl border border-[#7B2CBF]/30 bg-[#12001F]/80 px-4 py-2.5 text-sm text-[#FFF8E7] outline-none transition focus:border-[#80FFDB]/60 focus:ring-2 focus:ring-[#80FFDB]/15"
                >
                    <option value="">Cualquier estado</option>
                    <option value="Near Mint (NM)">Near Mint (NM)</option>
                    <option value="Lightly Played (LP)">Lightly Played (LP)</option>
                    <option value="Moderately Played (MP)">Moderately Played (MP)</option>
                </select>
            </div>

            <div>
                <select
                    wire:model.live="filterLanguage"
                    class="w-full rounded-xl border border-[#7B2CBF]/30 bg-[#12001F]/80 px-4 py-2.5 text-sm text-[#FFF8E7] outline-none transition focus:border-[#80FFDB]/60 focus:ring-2 focus:ring-[#80FFDB]/15"
                >
                    <option value="">Cualquier idioma</option>
                    <option value="Español">Español</option>
                    <option value="Inglés">Inglés</option>
                    <option value="Japonés">Japonés</option>
                </select>
            </div>
        </div>
        <div class="hidden overflow-hidden rounded-2xl border border-[#7B2CBF]/25 bg-[#2B2D42]/60 shadow-xl md:block">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                <tr class="border-b border-[#7B2CBF]/25 bg-[#12001F]/35 text-xs font-semibold uppercase tracking-wider text-[#FFF8E7]/50">
                    <th class="py-4 px-6">Imagen</th>
                    <th class="py-4 px-6">Nombre / Set</th>
                    <th class="py-4 px-6">Idioma</th>
                    <th class="py-4 px-6">Variante</th>
                    <th class="py-4 px-6">Estado</th>
                    <th class="py-4 px-6 text-center">Cantidad</th>
                    <th class="py-4 px-6">Precio</th>
                    <th class="py-4 px-6 text-center">¿Activo?</th>
                    <th class="py-4 px-6 text-right">Acciones</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-[#7B2CBF]/15 text-sm">
                @forelse($products as $product)
                    <tr class="group transition-colors hover:bg-[#7B2CBF]/10">
                        <td class="py-4 px-6">
                            <img src="{{ $product->card->image_url ?? 'https://via.placeholder.com/150' }}" alt="card" class="w-12 h-16 object-contain rounded-md shadow-md group-hover:scale-105 transition-transform">
                        </td>
                        <td class="py-4 px-6">
                            <div class="font-medium text-white">{{ $product->card->name }}</div>
                            <div class="mt-0.5 text-xs text-[#FFF8E7]/40">{{ $product->card->set->name ?? 'Sin Set' }} (#{{ $product->card->card_number }}/{{ $product->card->set->set_total ?? '?' }})</div>
                        </td>
                        <td class="px-6 py-4 text-[#FFF8E7]/70">{{ $product->language }}</td>
                        <td class="py-4 px-6">
                            <span class="rounded-lg border border-[#7B2CBF]/35 bg-[#7B2CBF]/15 px-2.5 py-1 text-xs font-semibold text-[#FFF8E7]/75">{{ $product->variant }}</span>
                        </td>
                        <td class="py-4 px-6">
                                <span class="rounded-lg border border-[#80FFDB]/20 bg-[#80FFDB]/10 px-2.5 py-1 text-xs font-semibold text-[#80FFDB]">
                                    {{ $product->condition }}
                                </span>
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-[#FFF8E7]">{{ $product->stock }}</td>
                        <td class="px-6 py-4 font-black tabular-nums text-[#80FFDB]">${{ number_format($product->price, 0, ',', '.') }} CLP</td>
                        <td class="py-4 px-6 text-center">
                            <button
                                type="button"
                                wire:click="toggleActive({{ $product->id }})"
                                aria-label="Cambiar visibilidad de {{ $product->card->name }}"
                                aria-pressed="{{ $product->is_active ? 'true' : 'false' }}"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-[#80FFDB]/40 {{ $product->is_active ? 'bg-[#80FFDB]' : 'bg-[#FFF8E7]/20' }}"
                            >
                            <span
                                class="inline-block h-4 w-4 transform rounded-full bg-[#12001F] transition-transform {{ $product->is_active ? 'translate-x-6' : 'translate-x-1' }}"
                            ></span>
                            </button>
                        </td>
                        <td class="py-4 px-6 text-right space-x-2">
                            <button
                                type="button"
                                wire:click="edit({{ $product->id }})"
                                class="rounded-lg border border-[#7B2CBF]/35 px-3 py-1.5 text-xs font-semibold text-[#FFF8E7]/65 transition-colors hover:border-[#80FFDB]/30 hover:text-[#80FFDB]"
                            >
                                Editar
                            </button>
                            <button onclick="confirm('¿Seguro de remover esta carta del inventario?') || event.stopImmediatePropagation()" wire:click="delete({{ $product->id }})" class="text-red-400 hover:text-red-300 border border-transparent hover:border-red-500/20 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">Eliminar</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-gray-500">No hay productos registrados en tu inventario.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="p-4 border-t border-gray-800 bg-gray-900/50">
                {{ $products->links() }}
            </div>
        @endif
    </div>

        <div class="space-y-4 md:hidden">
            @forelse($products as $product)
                <article
                    wire:key="mobile-product-{{ $product->id }}"
                    class="overflow-hidden rounded-2xl border border-[#7B2CBF]/25 bg-[#2B2D42]/75 p-4 shadow-lg shadow-black/15"
                >
                    <div class="flex gap-4">
                        <img
                            src="{{ $product->card->image_url ?? 'https://via.placeholder.com/150' }}"
                            alt="{{ $product->card->name }}"
                            class="h-28 w-20 shrink-0 rounded-lg bg-[#12001F] object-contain"
                        >

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h2 class="font-bold leading-5 text-[#FFF8E7]">
                                        {{ $product->card->name }}
                                    </h2>

                                    <p class="mt-1 text-xs leading-5 text-[#FFF8E7]/40">
                                        {{ $product->card->set->name ?? 'Sin set' }}
                                        (#{{ $product->card->card_number }}/{{ $product->card->set->set_total ?? '?' }})
                                    </p>
                                </div>

                                <p class="shrink-0 whitespace-nowrap font-black tabular-nums text-[#80FFDB]">
                                    ${{ number_format($product->price, 0, ',', '.') }}
                                </p>
                            </div>

                            <div class="mt-3 flex flex-wrap gap-2">
                        <span class="rounded-lg bg-[#12001F]/70 px-2 py-1 text-[11px] font-semibold text-[#FFF8E7]/70">
                            {{ $product->language }}
                        </span>

                                <span class="rounded-lg border border-[#7B2CBF]/35 bg-[#7B2CBF]/15 px-2 py-1 text-[11px] font-semibold text-[#FFF8E7]/70">
                            {{ $product->variant }}
                        </span>

                                <span class="rounded-lg border border-[#80FFDB]/20 bg-[#80FFDB]/10 px-2 py-1 text-[11px] font-semibold text-[#80FFDB]">
                            {{ $product->condition }}
                        </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-3 rounded-xl bg-[#12001F]/55 p-3">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-[#FFF8E7]/35">
                                Stock
                            </p>

                            <p class="mt-1 font-bold text-[#FFF8E7]">
                                {{ $product->stock }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-[#FFF8E7]/35">
                                    Estado
                                </p>

                                <p class="mt-1 text-sm font-semibold text-[#FFF8E7]/70">
                                    {{ $product->is_active ? 'Activo' : 'Inactivo' }}
                                </p>
                            </div>

                            <button
                                type="button"
                                wire:click="toggleActive({{ $product->id }})"
                                aria-label="Cambiar visibilidad de {{ $product->card->name }}"
                                aria-pressed="{{ $product->is_active ? 'true' : 'false' }}"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-[#80FFDB]/40 {{ $product->is_active ? 'bg-[#80FFDB]' : 'bg-[#FFF8E7]/20' }}"
                            >
                                <span class="inline-block h-4 w-4 transform rounded-full bg-[#12001F] transition-transform {{ $product->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-3 border-t border-[#7B2CBF]/20 pt-4">
                        <button
                            type="button"
                            wire:click="edit({{ $product->id }})"
                            class="rounded-xl border border-[#7B2CBF]/35 px-4 py-2.5 text-sm font-semibold text-[#FFF8E7]/75 transition-colors hover:border-[#80FFDB]/30 hover:text-[#80FFDB]"
                        >
                            Editar
                        </button>

                        <button
                            type="button"
                            onclick="confirm('¿Seguro de remover esta carta del inventario?') || event.stopImmediatePropagation()"
                            wire:click="delete({{ $product->id }})"
                            class="rounded-xl border border-red-400/20 px-4 py-2.5 text-sm font-semibold text-red-300 transition-colors hover:border-red-400/40 hover:bg-red-400/10"
                        >
                            Eliminar
                        </button>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-[#7B2CBF]/25 bg-[#2B2D42]/60 px-5 py-10 text-center text-sm text-[#FFF8E7]/45">
                    No hay productos registrados en tu inventario.
                </div>
            @endforelse

            @if($products->hasPages())
                <div class="rounded-xl border border-[#7B2CBF]/20 bg-[#2B2D42]/50 p-4">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    @if($isOpen)
            <div class="fixed inset-0 z-[100] flex items-start justify-center overflow-y-auto p-4 outline-none sm:p-6 md:items-center">
                <div
                    class="fixed inset-0 bg-[#12001F]/90 backdrop-blur-md transition-opacity"
                    wire:click="closeModal()"
                ></div>

                <div class="relative z-50 w-full max-w-2xl">
                    <div class="relative flex max-h-[calc(100dvh-2rem)] w-full flex-col overflow-hidden rounded-3xl border border-[#7B2CBF]/30 bg-[#2B2D42] text-[#FFF8E7] shadow-[0_30px_100px_rgba(0,0,0,0.55)] outline-none sm:max-h-[calc(100dvh-3rem)]">

                        <div class="flex shrink-0 items-center justify-between border-b border-[#7B2CBF]/25 px-5 py-4 sm:px-6">
                            <h3 class="text-lg font-black text-[#FFF8E7]">
                                {{ $inventoryId ? 'Editar producto' : 'Publicar nuevo producto' }}
                            </h3>

                            <button
                                type="button"
                                wire:click="closeModal()"
                                aria-label="Cerrar modal"
                                class="flex h-9 w-9 items-center justify-center rounded-xl text-xl font-semibold text-[#FFF8E7]/55 transition-colors hover:bg-[#12001F]/50 hover:text-[#80FFDB]"
                            >
                                ×
                            </button>
                        </div>

                        <div class="min-h-0 space-y-5 overflow-y-auto p-5 sm:p-6">
                        @if(!$selectedCard)
                            <div>
                                <label class="mb-2 block text-xs font-medium uppercase tracking-wider text-[#FFF8E7]/55">
                                    Buscar carta base (ej.: Lucario o 179/189)
                                </label>
                                <input
                                    wire:model.live="cardSearch"
                                    type="search"
                                    placeholder="Escribe el nombre o número..."
                                    autocomplete="off"
                                    class="w-full rounded-xl border border-[#7B2CBF]/40 bg-[#12001F]/80 px-4 py-2.5 text-sm text-[#FFF8E7] outline-none transition placeholder:text-[#FFF8E7]/30 focus:border-[#80FFDB] focus:ring-1 focus:ring-[#80FFDB]"
                                >

                                @if(!empty($availableCards))
                                    <div class="mt-2 divide-y divide-[#7B2CBF]/20 overflow-hidden rounded-xl border border-[#7B2CBF]/30 bg-[#12001F]/80">
                                        @foreach($availableCards as $c)
                                            <button
                                                type="button"
                                                wire:key="available-card-{{ $c->id }}"
                                                wire:click="selectCard({{ $c->id }})"
                                                class="flex w-full items-center gap-3 p-3 text-left text-sm transition-colors hover:bg-[#7B2CBF]/15 focus:outline-none focus:bg-[#7B2CBF]/15"
                                            >
                                                <img
                                                    src="{{ $c->image_url }}"
                                                    alt="{{ $c->name }}"
                                                    class="h-12 w-9 shrink-0 rounded object-contain"
                                                >

                                                <div class="min-w-0">
                                                    <p class="font-semibold text-[#FFF8E7]">
                                                        {{ $c->name }}
                                                    </p>

                                                    <p class="mt-0.5 text-xs text-[#FFF8E7]/40">
                                                        {{ $c->set->name ?? 'Sin set' }}
                                                        #{{ $c->card_number }}/{{ $c->set->set_total ?? '?' }}
                                                    </p>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                                @error('card_id') <span class="text-red-500 text-xs mt-1 block">Debes seleccionar una carta.</span> @enderror
                            </div>
                            @else
                                <div class="flex flex-col gap-5 rounded-2xl border border-[#7B2CBF]/25 bg-[#12001F]/70 p-4 shadow-inner sm:flex-row sm:items-center sm:p-5">
                                    <img
                                        src="{{ $selectedCard->image_url }}"
                                        alt="{{ $selectedCard->name }}"
                                        class="mx-auto h-48 w-36 shrink-0 rounded-lg object-contain shadow-lg shadow-[#7B2CBF]/20 sm:mx-0 sm:h-44 sm:w-32"
                                    >

                                    <div class="min-w-0 flex-1 text-center sm:text-left">
                                        <div class="text-xl font-black tracking-tight text-[#FFF8E7] sm:text-2xl">
                                            {{ $selectedCard->name }}
                                        </div>

                                        <div class="mt-2 text-sm leading-6 text-[#FFF8E7]/55 sm:text-base">
                                            {{ $selectedCard->set->name ?? 'Sin set' }}

                                            <span class="font-semibold text-[#FFF8E7]/75">
                    (#{{ $selectedCard->card_number }}/{{ $selectedCard->set->set_total ?? '?' }})
                </span>
                                        </div>

                                        <div class="mt-2 text-xs font-bold uppercase tracking-wider text-[#80FFDB]">
                                            {{ $selectedCard->card_type ?? 'Pokémon' }}
                                        </div>

                                        @if(!$inventoryId)
                                            <button
                                                type="button"
                                                wire:click="$set('selectedCard', null)"
                                                class="mt-4 w-full rounded-xl border border-[#7B2CBF]/30 px-4 py-2.5 text-sm font-semibold text-[#FFF8E7]/65 transition-colors hover:border-[#80FFDB]/30 hover:text-[#80FFDB] sm:w-auto"
                                            >
                                                Cambiar carta
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div>
                                    <label class="mb-2 block text-xs font-medium uppercase tracking-wider text-[#FFF8E7]/55">
                                        Idioma
                                    </label>

                                    <select
                                        wire:model="language"
                                        class="w-full rounded-xl border border-[#7B2CBF]/40 bg-[#12001F]/80 px-4 py-2.5 text-sm text-[#FFF8E7] outline-none transition focus:border-[#80FFDB] focus:ring-1 focus:ring-[#80FFDB]"
                                    >
                                        <option value="Español">Español</option>
                                        <option value="Inglés">Inglés</option>
                                        <option value="Japonés">Japonés</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-2 block text-xs font-medium uppercase tracking-wider text-[#FFF8E7]/55">
                                        Condición
                                    </label>

                                    <select
                                        wire:model="condition"
                                        class="w-full rounded-xl border border-[#7B2CBF]/40 bg-[#12001F]/80 px-4 py-2.5 text-sm text-[#FFF8E7] outline-none transition focus:border-[#80FFDB] focus:ring-1 focus:ring-[#80FFDB]"
                                    >
                                        <option value="Near Mint (NM)">Near Mint (NM)</option>
                                        <option value="Lightly Played (LP)">Lightly Played (LP)</option>
                                        <option value="Moderately Played (MP)">Moderately Played (MP)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-2 block text-xs font-medium uppercase tracking-wider text-[#FFF8E7]/55">
                                        Variante
                                    </label>

                                    <select
                                        wire:model="variant"
                                        class="w-full rounded-xl border border-[#7B2CBF]/40 bg-[#12001F]/80 px-4 py-2.5 text-sm text-[#FFF8E7] outline-none transition focus:border-[#80FFDB] focus:ring-1 focus:ring-[#80FFDB]"
                                    >
                                        <option value="Normal">Normal</option>
                                        <option value="Reverse Holo">Reverse Holo</option>
                                        <option value="Holo">Holo</option>
                                        <option value="1st Edition">1st Edition</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-xs font-medium uppercase tracking-wider text-[#FFF8E7]/55">
                                        Precio (CLP)
                                    </label>

                                    <input
                                        wire:model="price"
                                        type="number"
                                        min="0"
                                        class="w-full rounded-xl border border-[#7B2CBF]/40 bg-[#12001F]/80 px-4 py-2.5 text-sm text-[#FFF8E7] outline-none transition focus:border-[#80FFDB] focus:ring-1 focus:ring-[#80FFDB]"
                                    >

                                    @error('price')
                                    <span class="mt-1 block text-xs text-red-400">
                {{ $message }}
            </span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="mb-2 block text-xs font-medium uppercase tracking-wider text-[#FFF8E7]/55">
                                        Stock
                                    </label>

                                    <input
                                        wire:model="stock"
                                        type="number"
                                        min="0"
                                        step="1"
                                        class="w-full rounded-xl border border-[#7B2CBF]/40 bg-[#12001F]/80 px-4 py-2.5 text-sm text-[#FFF8E7] outline-none transition focus:border-[#80FFDB] focus:ring-1 focus:ring-[#80FFDB]"
                                    >

                                    @error('stock')
                                    <span class="mt-1 block text-xs text-red-400">
                {{ $message }}
            </span>
                                    @enderror
                                </div>
                            </div>
                    </div>

                        <div class="flex shrink-0 flex-col-reverse gap-3 border-t border-[#7B2CBF]/25 bg-[#12001F]/35 p-5 sm:flex-row sm:items-center sm:justify-end">
                            <button
                                type="button"
                                wire:click="closeModal()"
                                class="w-full rounded-xl border border-[#7B2CBF]/30 bg-[#2B2D42] px-5 py-3 text-sm font-semibold text-[#FFF8E7]/70 transition-colors hover:border-[#80FFDB]/30 hover:text-[#80FFDB] sm:w-auto"
                            >
                                Cancelar
                            </button>

                            <button
                                type="button"
                                wire:click="store()"
                                class="w-full rounded-xl border border-[#7B2CBF] bg-[#7B2CBF] px-5 py-3 text-sm font-bold text-[#FFF8E7] shadow-lg shadow-[#7B2CBF]/20 transition-all hover:bg-[#5A189A] active:scale-[0.98] sm:w-auto"
                            >
                                Guardar cambios
                            </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
</div>
