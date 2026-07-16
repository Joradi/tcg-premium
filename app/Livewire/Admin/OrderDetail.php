<?php

namespace App\Livewire\Admin;

use App\Actions\Order\CancelPendingOrder;
use App\Models\Order;
use DomainException;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class OrderDetail extends Component
{
    public Order $order;

    public function mount(Order $order): void
    {
        abort_unless(
            auth()->check() && auth()->user()->is_admin,
            403,
        );

        $this->order = $order->load('items');
    }

    public function cancelOrder(): void
    {
        abort_unless(
            auth()->check() && auth()->user()->is_admin,
            403,
        );
        try {
            $this->order = app(CancelPendingOrder::class)
                ->handle($this->order);
        } catch (DomainException $exception) {
            $this->addError(
                'order',
                $exception->getMessage(),
            );

            return;
        }

        session()->flash(
            'message',
            'Pedido cancelado y stock restaurado.'
        );
    }

    public function render(): View
    {
        return view('livewire.admin.order-detail');
    }
}
