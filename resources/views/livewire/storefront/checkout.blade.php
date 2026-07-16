<div class="min-h-screen bg-[#12001F] px-4 py-12 text-[#FFF8E7] sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <header class="mb-8">
            <p class="text-sm font-bold uppercase tracking-[0.2em] text-[#80FFDB]">
                Checkout
            </p>

            <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">
                Finalizar compra
            </h1>
        </header>

        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_420px]">
            <form
                wire:submit="submit"
                class="rounded-3xl border border-[#7B2CBF]/25 bg-[#2B2D42]/55 p-6"
            >
                @error('cart')
                    <div
                        role="alert"
                        class="mb-6 rounded-xl border border-red-400/30 bg-red-500/10 px-4 py-3 text-sm font-semibold text-red-200"
                    >
                        {{ $message }}
                    </div>
                @enderror

                <h2 class="text-xl font-black">
                    Datos de contacto y despacho
                </h2>

                    <p class="mt-2 text-sm text-[#FFF8E7]/55">
                        Usaremos estos datos exclusivamente para gestionar y entregar tu pedido.
                    </p>

                    <div class="mt-6 grid gap-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="customer-name" class="mb-2 block text-sm font-bold">
                                Nombre completo
                            </label>

                            <input
                                id="customer-name"
                                type="text"
                                wire:model="customerName"
                                autocomplete="name"
                                class="w-full rounded-xl border border-[#7B2CBF]/30 bg-[#12001F]/60 px-4 py-3 text-[#FFF8E7] outline-none placeholder:text-[#FFF8E7]/30 focus:border-[#80FFDB]/60 focus:ring-4 focus:ring-[#80FFDB]/10"
                            >

                            @error('customerName')
                            <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="customer-email" class="mb-2 block text-sm font-bold">
                                Correo electrónico
                            </label>

                            <input
                                id="customer-email"
                                type="email"
                                wire:model="customerEmail"
                                autocomplete="email"
                                class="w-full rounded-xl border border-[#7B2CBF]/30 bg-[#12001F]/60 px-4 py-3 text-[#FFF8E7] outline-none placeholder:text-[#FFF8E7]/30 focus:border-[#80FFDB]/60 focus:ring-4 focus:ring-[#80FFDB]/10"
                            >

                            @error('customerEmail')
                            <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="customer-phone" class="mb-2 block text-sm font-bold">
                                Teléfono
                            </label>

                            <input
                                id="customer-phone"
                                type="tel"
                                wire:model="customerPhone"
                                autocomplete="tel"
                                placeholder="+56 9 1234 5678"
                                class="w-full rounded-xl border border-[#7B2CBF]/30 bg-[#12001F]/60 px-4 py-3 text-[#FFF8E7] outline-none placeholder:text-[#FFF8E7]/30 focus:border-[#80FFDB]/60 focus:ring-4 focus:ring-[#80FFDB]/10"
                            >

                            @error('customerPhone')
                            <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="shipping-address-1" class="mb-2 block text-sm font-bold">
                                Dirección
                            </label>

                            <input
                                id="shipping-address-1"
                                type="text"
                                wire:model="shippingAddressLine1"
                                autocomplete="address-line1"
                                class="w-full rounded-xl border border-[#7B2CBF]/30 bg-[#12001F]/60 px-4 py-3 text-[#FFF8E7] outline-none placeholder:text-[#FFF8E7]/30 focus:border-[#80FFDB]/60 focus:ring-4 focus:ring-[#80FFDB]/10"
                            >

                            @error('shippingAddressLine1')
                            <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="shipping-address-2" class="mb-2 block text-sm font-bold">
                                Departamento, casa u otra referencia
                            </label>

                            <input
                                id="shipping-address-2"
                                type="text"
                                wire:model="shippingAddressLine2"
                                autocomplete="address-line2"
                                class="w-full rounded-xl border border-[#7B2CBF]/30 bg-[#12001F]/60 px-4 py-3 text-[#FFF8E7] outline-none placeholder:text-[#FFF8E7]/30 focus:border-[#80FFDB]/60 focus:ring-4 focus:ring-[#80FFDB]/10"
                            >

                            @error('shippingAddressLine2')
                            <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping-city" class="mb-2 block text-sm font-bold">
                                Comuna o ciudad
                            </label>

                            <input
                                id="shipping-city"
                                type="text"
                                wire:model="shippingCity"
                                autocomplete="address-level2"
                                class="w-full rounded-xl border border-[#7B2CBF]/30 bg-[#12001F]/60 px-4 py-3 text-[#FFF8E7] outline-none placeholder:text-[#FFF8E7]/30 focus:border-[#80FFDB]/60 focus:ring-4 focus:ring-[#80FFDB]/10"
                            >

                            @error('shippingCity')
                            <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping-region" class="mb-2 block text-sm font-bold">
                                Región
                            </label>

                            <input
                                id="shipping-region"
                                type="text"
                                wire:model="shippingRegion"
                                autocomplete="address-level1"
                                class="w-full rounded-xl border border-[#7B2CBF]/30 bg-[#12001F]/60 px-4 py-3 text-[#FFF8E7] outline-none placeholder:text-[#FFF8E7]/30 focus:border-[#80FFDB]/60 focus:ring-4 focus:ring-[#80FFDB]/10"
                            >

                            @error('shippingRegion')
                            <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping-postal-code" class="mb-2 block text-sm font-bold">
                                Código postal
                            </label>

                            <input
                                id="shipping-postal-code"
                                type="text"
                                wire:model="shippingPostalCode"
                                autocomplete="postal-code"
                                class="w-full rounded-xl border border-[#7B2CBF]/30 bg-[#12001F]/60 px-4 py-3 text-[#FFF8E7] outline-none placeholder:text-[#FFF8E7]/30 focus:border-[#80FFDB]/60 focus:ring-4 focus:ring-[#80FFDB]/10"
                            >

                            @error('shippingPostalCode')
                            <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping-country" class="mb-2 block text-sm font-bold">
                                País
                            </label>

                            <input
                                id="shipping-country"
                                type="text"
                                wire:model="shippingCountry"
                                autocomplete="country-name"
                                class="w-full rounded-xl border border-[#7B2CBF]/30 bg-[#12001F]/60 px-4 py-3 text-[#FFF8E7] outline-none placeholder:text-[#FFF8E7]/30 focus:border-[#80FFDB]/60 focus:ring-4 focus:ring-[#80FFDB]/10"
                            >

                            @error('shippingCountry')
                            <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="submit"
                        @disabled($this->cartItems->isEmpty())
                        class="mt-8 flex w-full items-center justify-center rounded-xl border border-[#7B2CBF] bg-[#7B2CBF] px-6 py-4 font-bold text-[#FFF8E7] shadow-lg shadow-[#7B2CBF]/20 transition hover:bg-[#5A189A] disabled:cursor-not-allowed disabled:opacity-50"
                    >
        <span wire:loading.remove wire:target="submit">
            Confirmar pedido
        </span>

                        <span wire:loading wire:target="submit">
            Procesando...
        </span>
                    </button>
                </form>
            <aside class="rounded-3xl border border-[#7B2CBF]/25 bg-[#2B2D42]/55 p-6">
                <h2 class="text-xl font-black">
                    Resumen del pedido
                </h2>

                <div class="mt-6 space-y-4">
                    @forelse($this->cartItems as $item)
                        <article class="flex gap-4 border-b border-[#7B2CBF]/20 pb-4">
                            @if($item->inventory->card->image_url)
                                <img
                                    src="{{ $item->inventory->card->image_url }}"
                                    alt="{{ $item->inventory->card->name }}"
                                    class="h-24 w-16 shrink-0 rounded-lg object-cover"
                                >
                            @endif

                            <div class="min-w-0 flex-1">
                                <h3 class="font-bold text-[#FFF8E7]">
                                    {{ $item->inventory->card->name }}
                                </h3>

                                <p class="mt-1 text-sm text-[#FFF8E7]/55">
                                    {{ $item->quantity }} {{ $item->quantity === 1 ? 'unidad' : 'unidades' }}
                                </p>

                                <p class="mt-2 font-black tabular-nums text-[#80FFDB]">
                                    ${{ number_format(
                                        $item->quantity * $item->inventory->price,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </p>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-[#7B2CBF]/35 px-4 py-8 text-center">
                            <p class="text-[#FFF8E7]/60">
                                El carrito está vacío.
                            </p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-6 border-t border-[#7B2CBF]/25 pt-5">
                    <div class="flex items-baseline justify-between gap-4">
                        <span class="font-bold">
                            Total
                        </span>

                        <span class="text-2xl font-black tabular-nums text-[#80FFDB]">
                            ${{ number_format($this->cartTotal, 0, ',', '.') }}
                        </span>
                    </div>

                    <p class="mt-2 text-right text-xs text-[#FFF8E7]/40">
                        Precios finales con IVA incluido
                    </p>
                </div>
            </aside>
        </div>
    </div>
</div>
