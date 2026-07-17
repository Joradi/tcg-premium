<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('registration is rate limited after five attempts from the same ip', function () {
    $this->withServerVariables([
        'REMOTE_ADDR' => '203.0.113.10',
    ]);

    foreach (range(1, 5) as $attempt) {
        $this->post('/register', [
            'name' => '',
            'email' => "invalid-{$attempt}@example.com",
            'password' => '',
            'password_confirmation' => '',
        ])->assertStatus(302);
    }

    $this->post('/register', [
        'name' => '',
        'email' => 'blocked@example.com',
        'password' => '',
        'password_confirmation' => '',
    ])->assertTooManyRequests();
});
