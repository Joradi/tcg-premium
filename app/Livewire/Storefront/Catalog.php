<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Inventory;

class Catalog extends Component
{
    use WithPagination;
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Inventory::with(['card.set'])
            ->where('is_active', true)
            ->where('stock', '>', 0);

        if(!empty($this->search))
        {
            $query->whereHas('card', function ($q) {
                $q->where('name', 'like', '%' .$this->search. '%');
            });
        }

        return view('livewire.storefront.catalog', [
            'products' => $query->orderBy('created_at', 'desc')->paginate(12)
            ]);
    }
}
