<x-guest-layout>
    <div class="mb-7 text-center">
        <h1 class="text-2xl font-black tracking-tight text-[#FFF8E7]">
            Crear nueva contraseña
        </h1>

        <p class="mt-2 text-sm leading-6 text-[#FFF8E7]/50">
            Define una contraseña nueva para recuperar el acceso a tu cuenta.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <input
            type="hidden"
            name="token"
            value="{{ $request->route('token') }}"
        >

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
                :value="old('email', $request->email)"
                required
                autofocus
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
                value="Nueva contraseña"
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
                value="Confirmar nueva contraseña"
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
            Guardar nueva contraseña
        </x-primary-button>

        <p class="text-center text-sm text-[#FFF8E7]/50">
            <a
                href="{{ route('login') }}"
                class="font-semibold text-[#80FFDB] transition-colors hover:text-[#FFF8E7]"
            >
                Volver a iniciar sesión
            </a>
        </p>
    </form>
</x-guest-layout>
