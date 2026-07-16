<div class="min-h-screen bg-[#12001F] px-4 py-16 text-[#FFF8E7] sm:px-6 lg:px-8">
    <div class="mx-auto max-w-3xl">
        <section class="rounded-3xl border border-[#80FFDB]/25 bg-[#2B2D42]/55 p-8 shadow-2xl">
            <div class="text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border border-[#80FFDB]/40 bg-[#80FFDB]/10 text-3xl text-[#80FFDB]">
                    ✓
                </div>

                <p class="mt-6 text-sm font-bold uppercase tracking-[0.2em] text-[#80FFDB]">
                    Pedido registrado
                </p>

                <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">
                    Pedido #{{ $order->id }}
                </h1>

                <p class="mt-4 text-[#FFF8E7]/60">
                    Tu pedido fue creado correctamente y quedó pendiente de pago.
                </p>
            </div>

            <div class="mt-8 grid gap-4 rounded-2xl border border-[#7B2CBF]/25 bg-[#12001F]/35 p-5 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-[#FFF8E7]/40">
                        Cliente
                    </p>

                    <p class="mt-1 font-bold">
                        {{ $order->customer_name }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-[#FFF8E7]/40">
                        Correo
                    </p>

                    <p class="mt-1 break-all font-bold">
                        {{ $order->customer_email }}
                    </p>
                </div>
            </div>

            <div class="mt-8">
                <h2 class="text-xl font-black">
                    Productos
                </h2>

                <div class="mt-4 divide-y divide-[#7B2CBF]/20">
                    @foreach($order->items as $item)
                        <article class="flex items-start justify-between gap-4 py-4">
                            <div class="min-w-0">
                                <h3 class="font-bold">
                                    {{ $item->card_name }}
                                </h3>

                                <p class="mt-1 text-sm text-[#FFF8E7]/50">
                                    {{ $item->quantity }}
                                    {{ $item->quantity === 1 ? 'unidad' : 'unidades' }}
                                    · {{ $item->language }}
                                    · {{ $item->condition }}
                                </p>
                            </div>

                            <p class="shrink-0 font-black tabular-nums text-[#80FFDB]">
                                ${{ number_format($item->subtotal, 0, ',', '.') }}
                            </p>
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 space-y-3 border-t border-[#7B2CBF]/25 pt-6">
                <div class="flex justify-between gap-4 text-sm text-[#FFF8E7]/60">
                    <span>Productos</span>

                    <span class="tabular-nums">
                        ${{ number_format($order->subtotal, 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex justify-between gap-4 text-sm text-[#FFF8E7]/60">
                    <span>IVA incluido</span>

                    <span class="tabular-nums">
                        ${{ number_format($order->tax_total, 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex justify-between gap-4 text-sm text-[#FFF8E7]/60">
                    <span>Despacho</span>

                    <span class="tabular-nums">
                        ${{ number_format($order->shipping_total, 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex items-baseline justify-between gap-4 border-t border-[#7B2CBF]/25 pt-4">
                    <span class="text-lg font-black">
                        Total
                    </span>

                    <span class="text-2xl font-black tabular-nums text-[#80FFDB]">
                        ${{ number_format($order->total, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <div class="mt-8 text-center">
                <a
                    href="{{ route('storefront.catalog') }}"
                    class="inline-flex rounded-xl border border-[#7B2CBF] bg-[#7B2CBF] px-6 py-3 font-bold text-[#FFF8E7] transition hover:bg-[#5A189A]"
                >
                    Volver al catálogo
                </a>
            </div>
        </section>
    </div>
</div>
