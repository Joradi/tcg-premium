<x-guest-layout>
    <div class="mb-7 text-center">
        <h1 class="text-2xl font-black tracking-tight text-[#FFF8E7]">
            Recuperar contraseña
        </h1>

        <p class="mt-2 text-sm leading-6 text-[#FFF8E7]/50">
            Ingresa tu correo electrónico y te enviaremos un enlace para crear una nueva contraseña.
        </p>
    </div>

    <x-auth-session-status
        class="mb-5"
        :status="session('status')"
    />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label
                for="email"
                value="Correo electrónico"
            />

            <x-text-input
                id="email"
                class="mt-1 block w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="email"
            />

            <x-input-error
                :messages="$errors->get('email')"
                class="mt-2"
            />
        </div>

        <x-primary-button class="w-full">
            Enviar enlace de recuperación
        </x-primary-button>

        <p class="text-center text-sm text-[#FFF8E7]/50">
            ¿Recordaste tu contraseña?

            <a
                href="{{ route('login') }}"
                class="font-semibold text-[#80FFDB] transition-colors hover:text-[#FFF8E7]"
            >
                Inicia sesión
            </a>
        </p>
    </form>
</x-guest-layout>
