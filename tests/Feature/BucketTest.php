<?php

use App\Models\User;

test('bucket screen can be rendered for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/bucket');

    $response->assertStatus(200);
});

test('unauthenticated users are redirected from bucket to login', function () {
    $response = $this->get('/bucket');

    $response->assertRedirect('/login');
});
