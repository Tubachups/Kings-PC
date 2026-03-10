<?php

use Illuminate\Support\Facades\Storage;

function sampleJpegBytes(): string
{
    if (function_exists('imagecreatetruecolor') && function_exists('imagejpeg')) {
        $image = imagecreatetruecolor(2, 2);
        if ($image !== false) {
            $background = imagecolorallocate($image, 32, 120, 220);
            imagefill($image, 0, 0, $background);

            ob_start();
            imagejpeg($image, null, 85);
            $bytes = ob_get_clean();
            imagedestroy($image);

            if (is_string($bytes) && $bytes !== '') {
                return $bytes;
            }
        }
    }

    $base64 = '/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxISEhUVExIVFhUXFxgYFxgYGBcXGBcYFxcXFxgYFxgYHSggGBolHRcXITEhJSorLi4uFx8zODMsNygtLisBCgoKDg0OGxAQGy0lICUtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLf/AABEIAAEAAgMBIgACEQEDEQH/xAAXAAEAAwAAAAAAAAAAAAAAAAAAAQIF/8QAFhEBAQEAAAAAAAAAAAAAAAAAABEB/9oADAMBAAIQAxAAAAGjA//EABkQAAIDAQAAAAAAAAAAAAAAAAECAAMREv/aAAgBAQABBQK4xYt1/8QAFhEBAQEAAAAAAAAAAAAAAAAAABEh/9oACAEDAQE/AYf/xAAVEQEBAAAAAAAAAAAAAAAAAAABEP/aAAgBAgEBPwFH/8QAGhAAAgIDAAAAAAAAAAAAAAAAAAERITFBUf/aAAgBAQAGPwK5n1Kf/8QAGhABAAMBAQEAAAAAAAAAAAAAAQARITFBUf/aAAgBAQABPyF9vJQ9K6o8rVQ0dT//2gAMAwEAAgADAAAAEP8A/8QAFhEBAQEAAAAAAAAAAAAAAAAAABEh/9oACAEDAQE/EGf/xAAVEQEBAAAAAAAAAAAAAAAAAAABEP/aAAgBAgEBPxBf/8QAHBABAAICAwEAAAAAAAAAAAAAAREhADEQQVFh/9oACAEBAAE/EI6GqkQW8Cj2K8h0E7Y8gQ6NqW//2Q==';

    $decoded = base64_decode($base64, true);

    return is_string($decoded) ? $decoded : '';
}

test('build image route converts stored jpeg blob to webp when supported', function () {
    Storage::fake();
    $buildPostId = 123;
    Storage::put("facebook_images/{$buildPostId}_preview_1.jpg", sampleJpegBytes());

    $response = $this->get(route('builds.image', ['buildPost' => $buildPostId, 'slot' => 1]));

    $response->assertOk();

    $gdInfo = function_exists('gd_info') ? gd_info() : [];
    $webpSupported = (bool) ($gdInfo['WebP Support'] ?? false);

    if ($webpSupported) {
        $response->assertHeader('Content-Type', 'image/webp');
        Storage::assertExists("facebook_images/{$buildPostId}_preview_1.webp");

        return;
    }

    $response->assertHeader('Content-Type', 'image/jpeg');
});
