<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class OrderManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterStatus = '';

    public function mount(): void
    {
        abort_unless(
            auth()->check() && auth()->user()->is_admin,
            403,
        );
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $search = trim($this->search);
        $orderNumber = ltrim($search, '#');
        $filterStatus = trim($this->filterStatus);

        $orders = Order::query()
            ->when($search !== '', function ($query) use ($search, $orderNumber): void {
                $query->where(function ($query) use ($search, $orderNumber): void {
                    $query
                        ->where('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%");

                    if (ctype_digit($orderNumber)) {
                        $query->orWhere('id', (int) $orderNumber);
                    }
                });
            })
            ->when($filterStatus !== '', function ($query) use ($filterStatus): void {
                $query->where('status', $filterStatus);
            })
            ->latest()
            ->paginate(15);

        return view('livewire.admin.order-manager', [
            'orders' => $orders,
        ]);
    }
}
