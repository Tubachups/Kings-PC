<?php

use App\Models\User;

test('appearance page applies the saved color theme class to the app shell', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->withCookie('appearance', 'coffee')
        ->get(route('appearance.edit'));

    $response
        ->assertOk()
        ->assertSee('class="theme-coffee"', false)
        ->assertDontSee('class="dark"', false);
});

test('appearance page applies dark mode when the saved appearance is dark', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->withCookie('appearance', 'dark')
        ->get(route('appearance.edit'));

    $response
        ->assertOk()
        ->assertSee('class="dark"', false);
});
