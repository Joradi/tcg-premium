<x-guest-layout>
    <div class="mb-7 text-center">
        <h1 class="text-2xl font-black tracking-tight text-[#FFF8E7]">
            Verifica tu correo
        </h1>

        <p class="mt-2 text-sm leading-6 text-[#FFF8E7]/50">
            Te enviamos un enlace de verificación. Revisa tu bandeja de entrada antes de continuar.
        </p>
    </div>

    @if (session('status') === 'verification-link-sent')
        <div
            class="mb-5 rounded-xl border border-[#80FFDB]/25 bg-[#80FFDB]/10 px-4 py-3 text-sm leading-6 text-[#80FFDB]"
            role="status"
        >
            Enviamos un nuevo enlace de verificación al correo registrado.
        </div>
    @endif

    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-primary-button class="w-full">
                Reenviar correo de verificación
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button
                type="submit"
                class="w-full rounded-xl border border-[#7B2CBF]/30 px-4 py-2.5 text-sm font-semibold text-[#FFF8E7]/60 transition-colors hover:border-[#80FFDB]/30 hover:text-[#80FFDB] focus:outline-none focus:ring-2 focus:ring-[#80FFDB]/40"
            >
                Cerrar sesión
            </button>
        </form>

        <p class="text-center text-sm text-[#FFF8E7]/45">
            El enlace puede tardar algunos minutos en llegar.
        </p>
    </div>
</x-guest-layout>
