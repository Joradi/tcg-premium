<?php

namespace App\Livewire\Storefront;

use App\Actions\Cart\ResolveActiveCart;
use App\Actions\Checkout\CreateOrderFromCart;
use App\Models\Cart;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Checkout extends Component
{
    public string $customerName = '';

    public string $customerEmail = '';

    public string $customerPhone = '';

    public string $shippingAddressLine1 = '';

    public string $shippingAddressLine2 = '';

    public string $shippingCity = '';

    public string $shippingRegion = '';

    public string $shippingPostalCode = '';

    public string $shippingCountry = 'Chile';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $this->customerName = $user->name;
        $this->customerEmail = $user->email;
    }

    #[Computed]
    public function cart(): ?Cart
    {
        $cart = app(ResolveActiveCart::class)->handle(
            sessionId: session()->getId(),
            userId: auth()->id(),
            createIfMissing: false,
        );

        return $cart?->load([
            'items.inventory.card.set',
        ]);
    }

    #[Computed]
    public function cartItems(): Collection
    {
        return $this->cart?->items ?? collect();
    }

    #[Computed]
    public function cartTotal(): int
    {
        return (int) $this->cartItems->sum(
            fn ($item): int => (
                (int) $item->quantity
                * (int) $item->inventory->price
            ),
        );
    }

    public function submit(): mixed
    {
        $validated = $this->validate();

        $rateLimitKey = $this->checkoutRateLimitKey();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $this->addError(
                'cart',
                'Demasiados intentos de compra. Espera un minuto antes de volver a intentarlo.',
            );

            return null;
        }

        $cart = $this->cart;

        if (! $cart) {
            $this->addError(
                'cart',
                'El carrito está vacío.',
            );

            return null;
        }

        RateLimiter::hit(
            $rateLimitKey,
            60,
        );

        try {
            $order = app(CreateOrderFromCart::class)->handle($cart, [
                'customer_name' => $validated['customerName'],
                'customer_email' => $validated['customerEmail'],
                'customer_phone' => $validated['customerPhone'] ?: null,
                'shipping_address_line_1' => $validated['shippingAddressLine1'],
                'shipping_address_line_2' => $validated['shippingAddressLine2'] ?: null,
                'shipping_city' => $validated['shippingCity'],
                'shipping_region' => $validated['shippingRegion'],
                'shipping_postal_code' => $validated['shippingPostalCode'] ?: null,
                'shipping_country' => $validated['shippingCountry'],
                'shipping_total' => 0,
            ]);
        } catch (DomainException $exception) {
            $this->addError(
                'cart',
                $exception->getMessage(),
            );

            return null;
        }

        session()->put(
            'checkout.completed_order_id',
            $order->id,
        );

        unset(
            $this->cart,
            $this->cartItems,
            $this->cartTotal,
        );

        $this->dispatch('cart-updated');

        return $this->redirectRoute(
            'storefront.checkout.confirmation',
        );
    }

    protected function rules(): array
    {
        return [
            'customerName' => ['required', 'string', 'max:120'],
            'customerEmail' => ['required', 'email', 'max:255'],
            'customerPhone' => ['nullable', 'string', 'max:30'],
            'shippingAddressLine1' => ['required', 'string', 'max:255'],
            'shippingAddressLine2' => ['nullable', 'string', 'max:255'],
            'shippingCity' => ['required', 'string', 'max:120'],
            'shippingRegion' => ['required', 'string', 'max:120'],
            'shippingPostalCode' => ['nullable', 'string', 'max:20'],
            'shippingCountry' => ['required', 'string', 'max:120'],
        ];
    }

    private function checkoutRateLimitKey(): string
    {
        $identity = auth()->check()
            ? 'user:'.auth()->id()
            : 'guest:'.session()->getId();

        return sprintf(
            'checkout-submit:%s|%s',
            $identity,
            request()->ip(),
        );
    }

    public function render(): View
    {
        return view('livewire.storefront.checkout');
    }
}
