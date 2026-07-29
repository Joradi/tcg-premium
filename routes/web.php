<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\IsAdmin;
use App\Livewire\Admin\InventoryManager;
use App\Livewire\Admin\OrderDetail;
use App\Livewire\Admin\OrderManager;
use App\Livewire\Storefront\Catalog;
use App\Livewire\Storefront\Checkout;
use App\Livewire\Storefront\CheckoutConfirmation;
use Illuminate\Support\Facades\Route;

Route::get('/', Catalog::class)->name('storefront.catalog');

Route::get('/dashboard', function () {
    return auth()->user()->is_admin
        ? redirect()->route('admin.inventario')
        : redirect()->route('storefront.catalog');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/admin/inventario', InventoryManager::class)
    ->middleware(['auth', IsAdmin::class])
    ->name('admin.inventario');

Route::get('/checkout', Checkout::class)
    ->name('storefront.checkout');

Route::get('/checkout/confirmacion', CheckoutConfirmation::class)
    ->name('storefront.checkout.confirmation');

Route::get('/admin/pedidos', OrderManager::class)
    ->middleware(['auth', IsAdmin::class])
    ->name('admin.pedidos');

Route::get('/admin/pedidos/{order}', OrderDetail::class)
    ->middleware(['auth', IsAdmin::class])
    ->name('admin.pedidos.show');

require __DIR__.'/auth.php';
