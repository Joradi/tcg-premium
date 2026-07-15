<x-guest-layout>
    <div class="mb-7 text-center">
        <h1 class="text-2xl font-black tracking-tight text-[#FFF8E7]">
            Crear una cuenta
        </h1>

        <p class="mt-2 text-sm text-[#FFF8E7]/50">
            Regístrate para comprar y gestionar tus pedidos.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label
                for="name"
                value="Nombre"
            />

            <x-text-input
                id="name"
                class="mt-1 block w-full"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
            />

            <x-input-error
                :messages="$errors->get('name')"
                class="mt-2"
            />
        </div>

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
                autocomplete="email"
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
                autocomplete="new-password"
            />

            <x-input-error
                :messages="$errors->get('password')"
                class="mt-2"
            />
        </div>

        <div>
            <x-input-label
                for="password_confirmation"
                value="Confirmar contraseña"
            />

            <x-text-input
                id="password_confirmation"
                class="mt-1 block w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />

            <x-input-error
                :messages="$errors->get('password_confirmation')"
                class="mt-2"
            />
        </div>

        <x-primary-button class="w-full">
            Crear cuenta
        </x-primary-button>

        <p class="text-center text-sm text-[#FFF8E7]/50">
            ¿Ya tienes una cuenta?

            <a
                href="{{ route('login') }}"
                class="font-semibold text-[#80FFDB] transition-colors hover:text-[#FFF8E7]"
            >
                Inicia sesión
            </a>
        </p>
    </form>
</x-guest-layout>
