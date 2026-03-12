<?php

use App\Models\BuildPost;
use Illuminate\Support\Facades\Storage;

test('build image route serves webp blob from build post', function () {
    Storage::fake();

    $webpBytes = 'fake-webp-image-bytes';

    $buildPost = BuildPost::query()->create([
        'text' => 'Unit price: 10000',
        'image_preview_1_webp' => $webpBytes,
        'likes' => 1,
    ]);

    $response = $this->get(route('builds.image', ['buildPost' => $buildPost->id, 'slot' => 1]));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'image/webp');
    $response->assertContent($webpBytes);
    Storage::assertExists("facebook_images/{$buildPost->id}_preview_1.webp");
});
