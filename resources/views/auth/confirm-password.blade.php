<x-guest-layout>
    <div class="mb-7 text-center">
        <h1 class="text-2xl font-black tracking-tight text-[#FFF8E7]">
            Confirmar contraseña
        </h1>

        <p class="mt-2 text-sm leading-6 text-[#FFF8E7]/50">
            Esta es un área segura. Confirma tu contraseña antes de continuar.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label
                for="password"
                value="Contraseña"
            />

            <x-text-input
                id="password"
                class="mt-1 block w-full"
                type="password"
                name="password"
                required
                autofocus
                autocomplete="current-password"
            />

            <x-input-error
                :messages="$errors->get('password')"
                class="mt-2"
            />
        </div>

        <x-primary-button class="w-full">
            Confirmar contraseña
        </x-primary-button>

        <p class="text-center text-sm text-[#FFF8E7]/50">
            <a
                href="{{ route('storefront.catalog') }}"
                class="font-semibold text-[#80FFDB] transition-colors hover:text-[#FFF8E7]"
            >
                Volver al catálogo
            </a>
        </p>
    </form>
</x-guest-layout>
