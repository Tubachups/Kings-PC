<?php

use Inertia\Testing\AssertableInertia as Assert;

test('about page is available', function () {
    $response = $this->get(route('about'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('shop/About')
        ->has('name')
    );
});
