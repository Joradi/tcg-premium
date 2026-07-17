<?php

it('agrega encabezados de seguridad a las respuestas web', function () {
    $this->get(route('storefront.catalog'))
        ->assertOk()
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader(
            'Referrer-Policy',
            'strict-origin-when-cross-origin',
        )
        ->assertHeader(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=()',
        );
});

it('agrega hsts cuando la solicitud utiliza https', function () {
    $this->get('https://localhost/')
        ->assertOk()
        ->assertHeader(
            'Strict-Transport-Security',
            'max-age=31536000; includeSubDomains',
        );
});

it('no agrega hsts cuando la solicitud utiliza http', function () {
    $this->get('http://localhost/')
        ->assertOk()
        ->assertHeaderMissing('Strict-Transport-Security');
});
