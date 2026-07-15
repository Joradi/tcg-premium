<nav class="sticky top-0 z-50 border-b border-[#7B2CBF]/25 bg-[#2B2D42]/95 text-[#FFF8E7] shadow-lg shadow-black/20 backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-16 flex-wrap items-center justify-between gap-x-4 gap-y-3 py-3 sm:flex-nowrap">
            <a
                href="{{ route('storefront.catalog') }}"
                class="order-1 flex shrink-0 items-center gap-2.5 group"
            >
                <img
                    src="{{ asset('images/gosuto-aku-logo.png') }}"
                    alt=""
                    width="40"
                    height="40"
                    aria-hidden="true"
                    class="h-10 w-10 shrink-0 rounded-full border border-[#80FFDB]/25 object-contain shadow-lg shadow-[#7B2CBF]/25 transition-transform duration-300 group-hover:scale-105"
                >

                <span class="text-lg font-black tracking-tight text-[#FFF8E7] sm:text-xl">
                    Gosuto Aku
                </span>
            </a>

            <div class="order-3 flex w-full flex-wrap items-center gap-x-5 gap-y-2 border-t border-[#7B2CBF]/20 pt-3 sm:order-2 sm:w-auto sm:flex-1 sm:justify-end sm:border-0 sm:pt-0">
                <a
                    href="{{ route('storefront.catalog') }}"
                    class="text-sm font-semibold transition-colors hover:text-[#80FFDB]"
                >
                    Catálogo
                </a>

                @auth
                    @if(auth()->user()->is_admin)
                        <a
                            href="{{ route('admin.inventario') }}"
                            class="text-sm font-semibold text-[#80FFDB] transition-colors hover:text-[#FFF8E7]"
                        >
                            Panel Admin
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button
                            type="submit"
                            class="text-sm font-semibold text-[#FFF8E7]/65 transition-colors hover:text-[#80FFDB]"
                        >
                            Cerrar sesión
                        </button>
                    </form>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="text-sm font-semibold text-[#FFF8E7]/75 transition-colors hover:text-[#80FFDB]"
                    >
                        Iniciar sesión
                    </a>

                    <a
                        href="{{ route('register') }}"
                        class="text-sm font-semibold text-[#FFF8E7]/75 transition-colors hover:text-[#80FFDB]"
                    >
                        Registrarse
                    </a>
                @endauth
            </div>

            <div class="order-2 shrink-0 sm:order-3">
                <livewire:storefront.cart-widget />
            </div>
        </div>
    </div>
</nav>
