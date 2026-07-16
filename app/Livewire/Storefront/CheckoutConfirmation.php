<?php

namespace App\Livewire\Storefront;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CheckoutConfirmation extends Component
{
    public Order $order;

    public function mount(): void
    {
        $orderId = session('checkout.completed_order_id');

        abort_if(
            $orderId === null,
            404,
        );

        $this->order = Order::query()
            ->with('items')
            ->findOrFail($orderId);
    }

    public function render(): View
    {
        return view('livewire.storefront.checkout-confirmation');
    }
}
