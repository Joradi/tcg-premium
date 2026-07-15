<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Gosuto Aku - Acceso</title>
    <link
        rel="icon"
        type="image/png"
        sizes="512x512"
        href="{{ asset('images/gosuto-aku-logo.png') }}"
    >
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#12001F] font-sans text-[#FFF8E7] antialiased selection:bg-[#80FFDB] selection:text-[#12001F]">
<main class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-6">
    <div class="w-full max-w-md">
        <a
            href="{{ route('storefront.catalog') }}"
            class="mb-6 inline-flex items-center gap-2 rounded-lg text-sm font-semibold text-[#FFF8E7]/60 transition-colors hover:text-[#80FFDB] focus:outline-none focus:ring-2 focus:ring-[#80FFDB]/40"
        >
            <span aria-hidden="true">←</span>
            Volver al catálogo
        </a>
        <a
            href="{{ route('storefront.catalog') }}"
            class="group mx-auto mb-8 block w-fit"
            aria-label="Volver al catálogo de Gosuto Aku"
        >
            <img
                src="{{ asset('images/gosuto-aku-logo.png') }}"
                alt="Gosuto Aku"
                width="176"
                height="176"
                class="h-40 w-40 rounded-full border border-[#7B2CBF]/50 object-contain shadow-[0_20px_60px_rgba(90,24,154,0.35)] transition-transform duration-300 group-hover:scale-[1.03] sm:h-44 sm:w-44"
            >
        </a>

        <section class="overflow-hidden rounded-3xl border border-[#7B2CBF]/30 bg-[#2B2D42]/95 px-6 py-8 shadow-[0_30px_90px_rgba(0,0,0,0.45)] sm:px-8">
            {{ $slot }}
        </section>

        <p class="mt-6 text-center text-xs text-[#FFF8E7]/35">
            © {{ date('Y') }} Gosuto Aku
        </p>
    </div>
</main>
</body>
</html>
