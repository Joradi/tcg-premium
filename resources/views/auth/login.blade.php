<x-guest-layout>
    <div class="mb-7 text-center">
        <h1 class="text-2xl font-black tracking-tight text-[#FFF8E7]">
            Iniciar sesión
        </h1>

        <p class="mt-2 text-sm text-[#FFF8E7]/50">
            Accede a tu cuenta de Gosuto Aku.
        </p>
    </div>

    <x-auth-session-status
        class="mb-5"
        :status="session('status')"
    />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
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
                autocomplete="username"
            />

            <x-input-error
                :messages="$errors->get('email')"
                class="mt-2"
            />
        </div>

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
                autocomplete="current-password"
            />

            <x-input-error
                :messages="$errors->get('password')"
                class="mt-2"
            />
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="rounded border-[#7B2CBF]/40 bg-[#12001F] text-[#7B2CBF] shadow-sm focus:ring-[#80FFDB]/50 focus:ring-offset-[#2B2D42]"
                >

                <span class="ms-2 text-sm text-[#FFF8E7]/60">
                    Recordarme
                </span>
            </label>

            @if (Route::has('password.request'))
                <a
                    href="{{ route('password.request') }}"
                    class="text-sm font-semibold text-[#FFF8E7]/55 underline decoration-[#7B2CBF]/60 underline-offset-4 transition-colors hover:text-[#80FFDB] focus:outline-none focus:ring-2 focus:ring-[#80FFDB]/40"
                >
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        <x-primary-button class="w-full">
            Iniciar sesión
        </x-primary-button>

        <p class="text-center text-sm text-[#FFF8E7]/50">
            ¿No tienes una cuenta?

            <a
                href="{{ route('register') }}"
                class="font-semibold text-[#80FFDB] transition-colors hover:text-[#FFF8E7]"
            >
                Regístrate
            </a>
        </p>
    </form>
</x-guest-layout>
