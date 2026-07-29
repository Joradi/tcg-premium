<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gosuto Aku - TCG Store</title>
    <link
        rel="icon"
        type="image/png"
        sizes="512x512"
        href="{{ asset('images/gosuto-aku-logo.png') }}"
    >
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col overflow-x-hidden bg-[#12001F] text-[#FFF8E7] antialiased selection:bg-[#80FFDB] selection:text-[#12001F]">
@include('layouts.partials.navigation')

<main class="flex-grow">
    {{ $slot }}
</main>

@include('layouts.partials.footer')

</body>
</html>
