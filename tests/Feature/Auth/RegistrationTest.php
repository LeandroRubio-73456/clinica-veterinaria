<?php

test('public registration is disabled because accounts are created by clinic staff', function () {
    $response = $this->get('/register');

    $response->assertNotFound();
});

test('public users cannot create accounts directly', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertNotFound();
    $this->assertGuest();
});
