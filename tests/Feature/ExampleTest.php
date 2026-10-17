<?php

test('the application returns a successful response', function () {
    $this->artisan('migrate');
    $this->artisan('db:seed');

    $response = $this->get('/');

    $response->assertStatus(200);
});
