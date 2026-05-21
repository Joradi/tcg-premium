<?php

namespace App\Livewire\Admin;


use App\Models\Card;
use App\Models\Inventory;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryManager extends Component
{
    use WithPagination;

    public $search = '';
    public $filterCondition = '';
    public $filterLanguage = '';

    public $isOpen = false;
    public $inventoryId = null;
    public $card_id, $language = 'Español', $condition = 'Near Mint (NM)', $variant = 'Normal', $price = 0, $stock = 1, $is_active = true;
    public $cardSearch = '';
    public $selectedCard = null;

    protected function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Inventory::with(['card.set']);

        if (!empty($this->search)) {
            $query->whereHas('card', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->filterCondition)) {
            $query->where('condition', $this->filterCondition);
        }

        if (!empty($this->filterLanguage)) {
            $query->where('language', $this->filterLanguage);
        }

        // Buscador de cartas de la API/Base para asociar al inventario
        $availableCards = [];
        if (strlen($this->cardSearch) > 1) {
            $search = $this->cardSearch;

            // Detectamos si el texto tiene un "/" (Ej: 179/189)
            if (str_contains($search, '/')) {
                $parts = explode('/', $search);
                $cardNumber = trim($parts[0]); // El 179
                $setTotal = trim($parts[1]);   // El 189

                $availableCards = Card::with('set')
                    ->where('card_number', 'like', $cardNumber . '%')
                    ->whereHas('set', function ($q) use ($setTotal) {
                        $q->where('set_total', 'like', $setTotal . '%');
                    })
                    ->take(8)
                    ->get();
            } else {
                // Búsqueda clásica por nombre o número simple
                $availableCards = Card::with('set')
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('card_number', 'like', '%' . $search . '%')
                    ->take(8)
                    ->get();
            }
        }

        return view('livewire.admin.inventory-manager', [
            'products' => $query->orderBy('created_at', 'desc')->paginate(10),
            'availableCards' => $availableCards
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->inventoryId = null;
        $this->card_id = null;
        $this->selectedCard = null;
        $this->cardSearch = '';
        $this->language = 'Español';
        $this->condition = 'Near Mint (NM)';
        $this->price = 0;
        $this->stock = 1;
        $this->is_active = true;
        $this->variant = 'Normal';
    }

    public function selectCard($cardId)
    {
        $this->card_id = $cardId;
        $this->selectedCard = Card::with('set')->find($cardId);
        $this->cardSearch = '';
    }

    public function store()
    {
        $this->validate([
            'card_id' => 'required',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        Inventory::updateOrCreate(['id' => $this->inventoryId], [
            'card_id' => $this->card_id,
            'language' => $this->language,
            'condition' => $this->condition,
            'variant' => $this->variant,
            'price' => $this->price,
            'stock' => $this->stock,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', $this->inventoryId ? 'Producto actualizado.' : 'Producto creado');

        $this->closeModal();
    }

    public function edit($id)
    {
        $inventory = Inventory::with('card.set')->findOrFail($id);
        $this->inventoryId = $id;
        $this->card_id = $inventory->card_id;
        $this->language = $inventory->language;
        $this->condition = $inventory->condition;
        $this->variant = $inventory->variant;
        $this->price = $inventory->price;
        $this->stock = $inventory->stock;
        $this->is_active = $inventory->is_active;

        $this->openModal();
    }

    public function toggleActive($id)
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->is_active = !$inventory->is_active;
        $inventory->save();
    }

    public function delete($id)
    {
        Inventory::find($id)->delete();
        session()->flash('message', 'Producto eliminado.');
    }
}
