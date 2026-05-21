<div class="p-6 bg-gray-950 min-h-screen text-gray-100">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Mis productos en venta</h1>
            <p class="text-sm text-gray-400 mt-1">Gestiona el stock, precios, variantes y visibilidad de tus cartas en tiempo real.</p>
        </div>
        <button wire:click="create()" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl font-medium text-sm transition-all shadow-lg shadow-blue-600/20 active:scale-95">
            Publicar producto
        </button>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 text-green-400 rounded-xl text-sm">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="relative md:col-span-2">
            <input wire:model.live="search" type="text" placeholder="Busca por nombre de producto..." class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 transition-colors">
        </div>
        <div>
            <select wire:model.live="filterCondition" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500">
                <option value="">Cualquier Estado</option>
                <option value="Near Mint (NM)">Near Mint (NM)</option>
                <option value="Lightly Played (LP)">Lightly Played (LP)</option>
                <option value="Moderately Played (MP)">Moderately Played (MP)</option>
            </select>
        </div>
        <div>
            <select wire:model.live="filterLanguage" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500">
                <option value="">Cualquier Idioma</option>
                <option value="Español">Español</option>
                <option value="Inglés">Inglés</option>
                <option value="Japonés">Japonés</option>
            </select>
        </div>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                <tr class="border-b border-gray-800 text-gray-400 text-xs font-semibold uppercase tracking-wider bg-gray-900/50">
                    <th class="py-4 px-6">Imagen</th>
                    <th class="py-4 px-6">Nombre / Set</th>
                    <th class="py-4 px-6">Idioma</th>
                    <th class="py-4 px-6">Variante</th>
                    <th class="py-4 px-6">Estado</th>
                    <th class="py-4 px-6 text-center">Cantidad</th>
                    <th class="py-4 px-6">Precio</th>
                    <th class="py-4 px-6 text-center">¿Activo?</th>
                    <th class="py-4 px-6 text-right">Acciones</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-sm">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-800/30 transition-colors group">
                        <td class="py-4 px-6">
                            <img src="{{ $product->card->image_url ?? 'https://via.placeholder.com/150' }}" alt="card" class="w-12 h-16 object-contain rounded-md shadow-md group-hover:scale-105 transition-transform">
                        </td>
                        <td class="py-4 px-6">
                            <div class="font-medium text-white">{{ $product->card->name }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ $product->card->set->name ?? 'Sin Set' }} (#{{ $product->card->card_number }}/{{ $product->card->set->set_total ?? '?' }})</div>
                        </td>
                        <td class="py-4 px-6 text-gray-300">{{ $product->language }}</td>
                        <td class="py-4 px-6">
                            <span class="text-blue-400 font-medium text-xs">{{ $product->variant }}</span>
                        </td>
                        <td class="py-4 px-6">
                                <span class="px-2.5 py-1 bg-gray-800 border border-gray-700 text-gray-300 rounded-lg text-xs font-medium">
                                    {{ $product->condition }}
                                </span>
                        </td>
                        <td class="py-4 px-6 text-center font-medium text-gray-200">{{ $product->stock }}</td>
                        <td class="py-4 px-6 font-semibold text-emerald-400">${{ number_format($product->price, 0, ',', '.') }} CLP</td>
                        <td class="py-4 px-6 text-center">
                            <button wire:click="toggleActive({{ $product->id }})" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none {{ $product->is_active ? 'bg-emerald-500' : 'bg-gray-700' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $product->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        </td>
                        <td class="py-4 px-6 text-right space-x-2">
                            <button wire:click="edit({{ $product->id }})" class="text-gray-400 hover:text-white border border-gray-800 hover:border-gray-700 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">Editar</button>
                            <button onclick="confirm('¿Seguro de remover esta carta del inventario?') || event.stopImmediatePropagation()" wire:click="delete({{ $product->id }})" class="text-red-400 hover:text-red-300 border border-transparent hover:border-red-500/20 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">Eliminar</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-gray-500">No hay productos registrados en tu inventario.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="p-4 border-t border-gray-800 bg-gray-900/50">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity" wire:click="closeModal()"></div>

            <div class="relative w-full max-w-xl mx-auto my-6 z-50">
                <div class="relative flex flex-col w-full bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl outline-none text-gray-200">

                    <div class="flex items-center justify-between p-5 border-b border-gray-800">
                        <h3 class="text-lg font-semibold text-white">{{ $inventoryId ? 'Editar Producto' : 'Publicar Nuevo Producto' }}</h3>
                        <button wire:click="closeModal()" class="text-gray-400 hover:text-white transition-colors text-xl font-semibold">×</button>
                    </div>

                    <div class="p-6 space-y-4">
                        @if(!$selectedCard)
                            <div>
                                <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Buscar Carta Base (Ej: Lucario, o 179/189)</label>
                                <input wire:model.live="cardSearch" type="text" placeholder="Escribe el nombre o número..." class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:border-blue-500">

                                @if(!empty($availableCards))
                                    <div class="mt-2 bg-gray-950 border border-gray-800 rounded-xl overflow-hidden divide-y divide-gray-800">
                                        @foreach($availableCards as $c)
                                            <button type="button" wire:click="selectCard({{ $c->id }})" class="w-full flex items-center gap-3 p-3 text-left hover:bg-gray-800/50 transition-colors text-sm">
                                                <img src="{{ $c->image_url }}" class="w-8 h-10 object-contain">
                                                <div>
                                                    <span class="font-medium text-white">{{ $c->name }}</span>
                                                    <span class="text-xs text-gray-500 ml-2">({{ $c->set->name ?? '' }} #{{ $c->card_number }}/{{ $c->set->set_total ?? '?' }})</span>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                                @error('card_id') <span class="text-red-500 text-xs mt-1 block">Debes seleccionar una carta.</span> @enderror
                            </div>
                        @else
                            <div class="flex items-center gap-5 p-5 bg-gray-950 border border-gray-800 rounded-2xl shadow-inner">
                                <img src="{{ $selectedCard->image_url }}" class="w-32 h-44 object-contain rounded-lg shadow-lg shadow-blue-900/15">
                                <div class="flex-1">
                                    <div class="font-bold text-white text-2xl tracking-tight">{{ $selectedCard->name }}</div>
                                    <div class="text-base text-gray-400 mt-1.5">{{ $selectedCard->set->name ?? '' }} <span class="text-gray-300 font-medium">(#{{ $selectedCard->card_number }}/{{ $selectedCard->set->set_total ?? '?' }})</span></div>
                                    <div class="text-sm text-blue-400 mt-2.5 uppercase font-bold tracking-wider">{{ $selectedCard->card_type ?? 'Pokémon' }}</div>
                                </div>
                                @if(!$inventoryId)
                                    <button type="button" wire:click="$set('selectedCard', null)" class="text-sm text-gray-500 hover:text-red-400 underline transition-colors">Cambiar Carta</button>
                                @endif
                            </div>
                        @endif

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Idioma</label>
                                <select wire:model="language" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2 text-sm text-white focus:outline-none">
                                    <option value="Español">Español</option>
                                    <option value="Inglés">Inglés</option>
                                    <option value="Japonés">Japonés</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Condición</label>
                                <select wire:model="condition" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2 text-sm text-white focus:outline-none">
                                    <option value="Near Mint (NM)">Near Mint (NM)</option>
                                    <option value="Lightly Played (LP)">Lightly Played (LP)</option>
                                    <option value="Moderately Played (MP)">Moderately Played (MP)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Variante</label>
                                <select wire:model="variant" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2 text-sm text-white focus:outline-none border-blue-900/50">
                                    <option value="Normal">Normal</option>
                                    <option value="Reverse Holo">Reverse Holo</option>
                                    <option value="Holo">Holo</option>
                                    <option value="1st Edition">1st Edition</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Precio (CLP)</label>
                                <input wire:model="price" type="number" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:border-blue-500">
                                @error('price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Stock</label>
                                <input wire:model="stock" type="number" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:border-blue-500">
                                @error('stock') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end p-5 border-t border-gray-800 gap-3">
                        <button type="button" wire:click="closeModal()" class="bg-gray-800 hover:bg-gray-700 text-gray-300 px-4 py-2 rounded-xl text-sm font-medium transition-colors">Cancelar</button>
                        <button type="button" wire:click="store()" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2 rounded-xl text-sm font-medium transition-all shadow-lg shadow-blue-600/20">Guardar Cambios</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
