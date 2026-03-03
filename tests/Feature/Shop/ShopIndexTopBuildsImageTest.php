<?php

use App\Models\BuildPost;
use Inertia\Testing\AssertableInertia as Assert;

test('shop index top builds uses application image route when source exists', function () {
    $buildPost = BuildPost::query()->create([
        'text' => 'Unit price: 10000',
        'image_preview_1' => 'https://scontent.fakesource/image.jpg',
        'image_preview_1_blob' => 'fake-image-bytes',
        'likes' => 5,
    ]);

    $response = $this->get(route('shop'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Shop/Index')
        ->has('topBuilds', 1)
        ->where('topBuilds.0.image_preview_1', route('builds.image', ['buildPost' => $buildPost->id, 'slot' => 1]))
    );
});
