<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>gosuto Aku - TCG Store</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-950 text-gray-200 antialiased selection:bg-blue-500 selection:text-white flex flex-col min-h-screen">

<nav class="bg-gray-900 border-b border-gray-800 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-8">
                <a href="{{ route('storefront.catalog') }}" class="flex-shrink-0 flex items-center gap-2 group">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-black group-hover:bg-blue-500 transition-colors shadow-lg shadow-blue-900/20">
                        gA
                    </div>
                    <span class="font-bold text-xl text-white tracking-tight">gosuto Aku</span>
                </a>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('storefront.catalog') }}" class="text-sm font-medium text-gray-300 hover:text-white transition-colors">Catálogo</a>

                <livewire:storefront.cart-widget />

                <div class="h-6 w-px bg-gray-800 mx-2"></div>

                @auth
                    @if(auth()->user()->is_admin)
                        <a href="{{ route('admin.inventario') }}" class="text-sm font-medium text-blue-400 hover:text-blue-300 transition-colors">Panel Admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-gray-400 hover:text-red-400 transition-colors">Cerrar Sesión</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-300 hover:text-white transition-colors">Iniciar Sesión</a>
                    <a href="{{ route('register') }}" class="text-sm font-medium text-gray-300 hover:text-white transition-colors">Registrarse</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<main class="flex-grow">
    {{ $slot }}
</main>

<footer class="bg-gray-950 border-t border-gray-900 py-8 text-center mt-12">
    <p class="text-gray-600 text-sm">© {{ date('Y') }} gosuto Aku. Todos los derechos reservados.</p>
</footer>

</body>
</html>
