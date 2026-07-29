<div class="min-h-screen bg-[#12001F] px-4 py-12 text-[#FFF8E7] sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <header class="mb-8">
            <p class="text-sm font-bold uppercase tracking-[0.2em] text-[#80FFDB]">
                Administración
            </p>

            <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">
                Administración de pedidos
            </h1>

            <p class="mt-3 text-sm text-[#FFF8E7]/55">
                Revisa los pedidos creados desde el checkout.
            </p>
        </header>

        <div class="mb-6 grid gap-4 md:grid-cols-[minmax(0,1fr)_240px]">
            <div>
                <label
                    for="order-search"
                    class="mb-2 block text-sm font-bold"
                >
                    Buscar pedidos
                </label>

                <input
                    id="order-search"
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Número, nombre o correo"
                    class="w-full rounded-xl border border-[#7B2CBF]/30 bg-[#2B2D42]/55 px-4 py-3 text-[#FFF8E7] outline-none placeholder:text-[#FFF8E7]/30 focus:border-[#80FFDB]/60 focus:ring-4 focus:ring-[#80FFDB]/10"
                >
            </div>

            <div>
                <label
                    for="order-status"
                    class="mb-2 block text-sm font-bold"
                >
                    Estado
                </label>

                <select
                    id="order-status"
                    wire:model.live="filterStatus"
                    class="w-full rounded-xl border border-[#7B2CBF]/30 bg-[#2B2D42] px-4 py-3 text-[#FFF8E7] outline-none focus:border-[#80FFDB]/60 focus:ring-4 focus:ring-[#80FFDB]/10"
                >
                    <option value="">Todos los estados</option>
                    <option value="pending">Pendientes</option>
                    <option value="cancelled">Cancelados</option>
                </select>
            </div>
        </div>
        <section class="overflow-hidden rounded-3xl border border-[#7B2CBF]/25 bg-[#2B2D42]/55 shadow-2xl">
            @forelse($orders as $order)
                <article
                    wire:key="order-{{ $order->id }}"
                    class="grid gap-4 border-b border-[#7B2CBF]/20 p-6 last:border-b-0 md:grid-cols-[1fr_1fr_auto]"
                >
                    <div>
                        <p class="font-black text-[#80FFDB]">
                            Pedido #{{ $order->id }}
                        </p>

                        <p class="mt-2 font-bold">
                            {{ $order->customer_name }}
                        </p>

                        <p class="mt-1 text-sm text-[#FFF8E7]/55">
                            {{ $order->customer_email }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-[#FFF8E7]/40">
                            Estado
                        </p>

                        <p class="mt-2 font-bold">
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
                        </p>

                        <p class="mt-2 text-sm text-[#FFF8E7]/55">
                            {{ $order->created_at
                                ->copy()
                                ->timezone(config('app.display_timezone'))
                                ->format('d-m-Y H:i') }}
                        </p>
                    </div>

                    <div class="md:text-right">
                        <p class="text-xs font-bold uppercase tracking-wider text-[#FFF8E7]/40">
                            Total
                        </p>

                        <p class="mt-2 text-xl font-black tabular-nums text-[#80FFDB]">
                            ${{ number_format($order->total, 0, ',', '.') }}
                        </p>

                        <a
                            href="{{ route('admin.pedidos.show', $order) }}"
                            class="mt-4 inline-flex rounded-xl border border-[#7B2CBF] px-4 py-2 text-sm font-bold text-[#FFF8E7] transition hover:bg-[#7B2CBF]"
                        >
                            Ver detalle
                        </a>
                    </div>
                </article>
            @empty
                <div class="p-12 text-center">
                    <p class="text-[#FFF8E7]/55">
                        Todavía no existen pedidos.
                    </p>
                </div>
            @endforelse
        </section>

        @if($orders->hasPages())
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
