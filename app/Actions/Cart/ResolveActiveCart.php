<?php

namespace App\Actions\Cart;

use App\Models\Cart;
use Illuminate\Support\Facades\DB;

final class ResolveActiveCart
{
    public function handle(
        string $sessionId,
        ?int $userId = null,
        bool $createIfMissing = true,
    ): ?Cart {
        return DB::transaction(function () use (
            $sessionId,
            $userId,
            $createIfMissing,
        ): ?Cart {
            $sessionCart = Cart::query()
                ->where('session_id', $sessionId)
                ->lockForUpdate()
                ->first();

            $userCart = $userId !== null
                ? Cart::query()
                    ->where('user_id', $userId)
                    ->lockForUpdate()
                    ->first()
                : null;

            if ($sessionCart && $userCart && ! $sessionCart->is($userCart)) {
                $this->mergeCartItems(
                    sourceCart: $sessionCart,
                    targetCart: $userCart,
                );

                $sessionCart->delete();

                $userCart->update([
                    'session_id' => $sessionId,
                ]);

                return $userCart->refresh();
            }

            if ($sessionCart) {
                if ($userId !== null && $sessionCart->user_id === null) {
                    $sessionCart->update([
                        'user_id' => $userId,
                    ]);
                }

                return $sessionCart->refresh();
            }

            if ($userCart) {
                $userCart->update([
                    'session_id' => $sessionId,
                ]);

                return $userCart->refresh();
            }

            if (! $createIfMissing) {
                return null;
            }

            return Cart::query()
                ->create([
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                ]);
        });
    }

    private function mergeCartItems(
        Cart $sourceCart,
        Cart $targetCart,
    ): void {
        $sourceItems = $sourceCart->items()
            ->lockForUpdate()
            ->get();

        foreach ($sourceItems as $sourceItem) {
            $targetItem = $targetCart->items()
                ->where('inventory_id', $sourceItem->inventory_id)
                ->lockForUpdate()
                ->first();

            if ($targetItem) {
                $targetItem->increment(
                    'quantity',
                    $sourceItem->quantity,
                );

                $sourceItem->delete();

                continue;
            }

            $sourceItem->update([
                'cart_id' => $targetCart->id,
            ]);
        }
    }
}
