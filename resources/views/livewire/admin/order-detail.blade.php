<div class="min-h-screen bg-[#12001F] px-4 py-12 text-[#FFF8E7] sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl">
        <header class="mb-8">
            <a
                href="{{ route('admin.pedidos') }}"
                class="text-sm font-bold text-[#80FFDB] hover:underline"
            >
                ← Volver a pedidos
            </a>

            <p class="mt-6 text-sm font-bold uppercase tracking-[0.2em] text-[#80FFDB]">
                Detalle administrativo
            </p>

            <div class="mt-2 flex flex-wrap items-center justify-between gap-4">
                <h1 class="text-3xl font-black tracking-tight sm:text-4xl">
                    Pedido #{{ $order->id }}
                </h1>

                <span class="rounded-full border border-[#80FFDB]/30 bg-[#80FFDB]/10 px-4 py-2 text-sm font-bold text-[#80FFDB]">
                    @switch($order->status)
                        @case('pending')
                            Pendiente
                            @break

                        @case('cancelled')
                            Cancelado
                            @break

                        @default
                            {{ ucfirst($order->status) }}
                    @endswitch
                </span>
            </div>

            <p class="mt-3 text-sm text-[#FFF8E7]/50">
                Creado el {{ $order->created_at->format('d-m-Y H:i') }}
            </p>
        </header>

        @if(session()->has('message'))
            <div
                role="status"
                class="mb-6 rounded-xl border border-[#80FFDB]/30 bg-[#80FFDB]/10 px-4 py-3 text-sm font-semibold text-[#80FFDB]"
            >
                {{ session('message') }}
            </div>
        @endif

        @error('order')
        <div
            role="alert"
            class="mb-6 rounded-xl border border-red-400/30 bg-red-500/10 px-4 py-3 text-sm font-semibold text-red-200"
        >
            {{ $message }}
        </div>
        @enderror

        @if($order->status === 'pending')
            <div class="mb-6 flex justify-end">
                <button
                    type="button"
                    wire:click="cancelOrder"
                    wire:loading.attr="disabled"
                    wire:target="cancelOrder"
                    wire:confirm="¿Confirmas que deseas cancelar este pedido y restaurar su inventario?"
                    class="rounded-xl border border-red-400/40 bg-red-500/10 px-5 py-3 font-bold text-red-200 transition hover:bg-red-500/20 disabled:cursor-not-allowed disabled:opacity-50"
                >
            <span wire:loading.remove wire:target="cancelOrder">
                Cancelar pedido
            </span>

                    <span wire:loading wire:target="cancelOrder">
                Cancelando...
            </span>
                </button>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="rounded-3xl border border-[#7B2CBF]/25 bg-[#2B2D42]/55 p-6">
                <h2 class="text-xl font-black">
                    Cliente
                </h2>

                <dl class="mt-5 space-y-4">
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wider text-[#FFF8E7]/40">
                            Nombre
                        </dt>

                        <dd class="mt-1 font-bold">
                            {{ $order->customer_name }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wider text-[#FFF8E7]/40">
                            Correo
                        </dt>

                        <dd class="mt-1 break-all">
                            {{ $order->customer_email }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wider text-[#FFF8E7]/40">
                            Teléfono
                        </dt>

                        <dd class="mt-1">
                            {{ $order->customer_phone ?: 'No informado' }}
                        </dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-3xl border border-[#7B2CBF]/25 bg-[#2B2D42]/55 p-6">
                <h2 class="text-xl font-black">
                    Dirección de despacho
                </h2>

                <div class="mt-5 space-y-1 text-[#FFF8E7]/75">
                    <p>{{ $order->shipping_address_line_1 }}</p>

                    @if($order->shipping_address_line_2)
                        <p>{{ $order->shipping_address_line_2 }}</p>
                    @endif

                    <p>
                        {{ $order->shipping_city }},
                        {{ $order->shipping_region }}
                    </p>

                    @if($order->shipping_postal_code)
                        <p>{{ $order->shipping_postal_code }}</p>
                    @endif

                    <p>{{ $order->shipping_country }}</p>
                </div>
            </section>
        </div>

        <section class="mt-6 rounded-3xl border border-[#7B2CBF]/25 bg-[#2B2D42]/55 p-6">
            <h2 class="text-xl font-black">
                Productos
            </h2>

            <div class="mt-5 divide-y divide-[#7B2CBF]/20">
                @foreach($order->items as $item)
                    <article class="flex items-start justify-between gap-5 py-5 first:pt-0 last:pb-0">
                        <div class="min-w-0">
                            <h3 class="font-black">
                                {{ $item->card_name }}
                            </h3>

                            <p class="mt-1 text-sm text-[#FFF8E7]/55">
                                {{ $item->set_name }}
                                · {{ $item->card_number }}
                            </p>

                            <p class="mt-1 text-sm text-[#FFF8E7]/55">
                                {{ $item->language }}
                                · {{ $item->condition }}
                                · {{ $item->variant }}
                            </p>

                            <p class="mt-2 text-sm font-bold">
                                {{ $item->quantity }} {{ $item->quantity === 1 ? 'unidad' : 'unidades' }}
                                × ${{ number_format($item->unit_price, 0, ',', '.') }}
                            </p>
                        </div>

                        <p class="shrink-0 font-black tabular-nums text-[#80FFDB]">
                            ${{ number_format($item->subtotal, 0, ',', '.') }}
                        </p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="mt-6 rounded-3xl border border-[#7B2CBF]/25 bg-[#2B2D42]/55 p-6">
            <h2 class="text-xl font-black">
                Totales
            </h2>

            <dl class="mt-5 space-y-3">
                <div class="flex justify-between gap-4 text-[#FFF8E7]/65">
                    <dt>Subtotal</dt>

                    <dd class="tabular-nums">
                        ${{ number_format($order->subtotal, 0, ',', '.') }}
                    </dd>
                </div>

                <div class="flex justify-between gap-4 text-[#FFF8E7]/65">
                    <dt>IVA incluido</dt>

                    <dd class="tabular-nums">
                        ${{ number_format($order->tax_total, 0, ',', '.') }}
                    </dd>
                </div>

                <div class="flex justify-between gap-4 text-[#FFF8E7]/65">
                    <dt>Despacho</dt>

                    <dd class="tabular-nums">
                        ${{ number_format($order->shipping_total, 0, ',', '.') }}
                    </dd>
                </div>

                <div class="flex justify-between gap-4 border-t border-[#7B2CBF]/25 pt-4 text-xl font-black">
                    <dt>Total</dt>

                    <dd class="tabular-nums text-[#80FFDB]">
                        ${{ number_format($order->total, 0, ',', '.') }}
                    </dd>
                </div>
            </dl>
        </section>
    </div>
</div>
