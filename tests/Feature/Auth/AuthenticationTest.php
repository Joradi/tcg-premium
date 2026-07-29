<?php

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Event;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('regular users are redirected to the catalog after login', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);

    $response->assertRedirect(
        route('storefront.catalog', absolute: false),
    );
});

test('administrators are redirected to inventory after login', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $response = $this->post('/login', [
        'email' => $admin->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($admin);

    $response->assertRedirect(
        route('admin.inventario', absolute: false),
    );
});

test('regular users are redirected from dashboard to the catalog', function () {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(
            route('storefront.catalog', absolute: false),
        );
});

test('administrators are redirected from dashboard to inventory', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertRedirect(
            route('admin.inventario', absolute: false),
        );
});

test('users can not authenticate with invalid password', function () {
    app()->setLocale('es');

    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();

    $response->assertSessionHasErrors([
        'email' => 'Estas credenciales no coinciden con nuestros registros.',
    ]);
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});

test('login is rate limited after five failed attempts', function () {
    Event::fake([
        Lockout::class,
    ]);

    $user = User::factory()->create();

    foreach (range(1, 5) as $_) {
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');
    }

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();

    $response->assertSessionHasErrors('email');

    Event::assertDispatched(Lockout::class);
});
