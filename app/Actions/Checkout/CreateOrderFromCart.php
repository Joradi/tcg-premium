<?php

namespace App\Actions\Checkout;

use App\Models\Cart;
use App\Models\Inventory;
use App\Models\Order;
use App\Support\IncludedTaxCalculator;
use DomainException;
use Illuminate\Support\Facades\DB;

final class CreateOrderFromCart
{
    public function __construct(
        private readonly IncludedTaxCalculator $taxCalculator
    ) {}

    public function handle(Cart $cart, array $customerData): Order
    {
        return DB::transaction(function () use ($cart, $customerData): Order {
            $cartItems = $cart->items()->get();

            if ($cartItems->isEmpty()) {
                throw new DomainException('El carrito está vacío.');
            }

            $subtotal = 0;
            $taxTotal = 0;
            $lines = [];

            foreach ($cartItems as $cartItem) {
                $inventory = Inventory::query()
                    ->with(['card.set'])
                    ->lockForUpdate()
                    ->findOrFail($cartItem->inventory_id);

                $quantity = (int) $cartItem->quantity;

                if ($quantity < 1) {
                    throw new DomainException(
                        'La cantidad del producto debe ser mayor que cero.'
                    );
                }

                if (! $inventory->is_active) {
                    throw new DomainException(
                        'Uno de los productos ya no está disponible.'
                    );
                }

                if ($inventory->stock < $quantity) {
                    throw new DomainException(
                        'No existe stock suficiente para completar el pedido.'
                    );
                }

                $unitPrice = (int) $inventory->price;
                $lineSubtotal = $unitPrice * $quantity;
                $lineTaxTotal = $this->taxCalculator->calculate($lineSubtotal);

                $subtotal += $lineSubtotal;
                $taxTotal += $lineTaxTotal;

                $lines[] = [
                    'inventory' => $inventory,
                    'quantity' => $quantity,
                    'attributes' => [
                        'inventory_id' => $inventory->id,
                        'card_name' => $inventory->card->name,
                        'set_name' => $inventory->card->set?->name,
                        'card_number' => $inventory->card->card_number,
                        'image_url' => $inventory->card->image_url,
                        'language' => $inventory->language,
                        'condition' => $inventory->condition,
                        'variant' => $inventory->variant,
                        'unit_price' => $unitPrice,
                        'quantity' => $quantity,
                        'subtotal' => $lineSubtotal,
                        'tax_rate' => IncludedTaxCalculator::DEFAULT_TAX_RATE,
                        'tax_total' => $lineTaxTotal,
                    ],
                ];
            }

            $shippingTotal = (int) ($customerData['shipping_total'] ?? 0);

            if ($shippingTotal < 0) {
                throw new DomainException(
                    'El costo de despacho no puede ser negativo.'
                );
            }

            $order = Order::create([
                'user_id' => $cart->user_id,
                'status' => 'pending',
                'customer_name' => $customerData['customer_name'],
                'customer_email' => $customerData['customer_email'],
                'customer_phone' => $customerData['customer_phone'] ?? null,
                'shipping_address_line_1' => $customerData['shipping_address_line_1'],
                'shipping_address_line_2' => $customerData['shipping_address_line_2'] ?? null,
                'shipping_city' => $customerData['shipping_city'],
                'shipping_region' => $customerData['shipping_region'],
                'shipping_postal_code' => $customerData['shipping_postal_code'] ?? null,
                'shipping_country' => $customerData['shipping_country'] ?? 'Chile',
                'subtotal' => $subtotal,
                'tax_total' => $taxTotal,
                'shipping_total' => $shippingTotal,
                'total' => $subtotal + $shippingTotal,

            ]);

            foreach ($lines as $line) {
                $order->items()->create($line['attributes']);

                $line['inventory']->decrement(
                    'stock',
                    $line['quantity'],
                );
            }

            $cart->items()->delete();

            return $order->load('items');
        });
    }
}
