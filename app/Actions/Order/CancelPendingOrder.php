<?php

namespace App\Actions\Order;

use App\Models\Inventory;
use App\Models\Order;
use DomainException;
use Illuminate\Support\Facades\DB;

final class CancelPendingOrder
{
    public function handle(Order $order): Order
    {
        return DB::transaction(function () use ($order): Order {
            $lockedOrder = Order::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($order->id);

            if ($lockedOrder->status !== 'pending') {
                throw new DomainException(
                    'Solo los pedidos pendientes pueden cancelarse.',
                );
            }

            foreach ($lockedOrder->items as $item) {
                if ($item->inventory_id === null) {
                    continue;
                }

                $inventory = Inventory::query()
                    ->lockForUpdate()
                    ->find($item->inventory_id);

                if (! $inventory) {
                    continue;
                }

                $inventory->increment(
                    'stock',
                    (int) $item->quantity,
                );
            }

            $lockedOrder->update([
                'status' => 'cancelled',
            ]);

            return $lockedOrder
                ->refresh()
                ->load('items');
        });
    }
}
