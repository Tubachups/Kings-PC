<?php

namespace App\Services\Shop;

use App\Models\BuildPost;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class BuildImageService
{
    public function serveBuildImage(int $buildPostId, int $slot): HttpResponse
    {
        if (! in_array($slot, [1, 2, 3, 4], true)) {
            abort(404);
        }

        $webpFilename = "facebook_images/{$buildPostId}_preview_{$slot}.webp";
        $sourceCacheKey = "build-image-source:{$buildPostId}:{$slot}";

        if (Storage::exists($webpFilename)) {
            Cache::put($sourceCacheKey, 'file:webp', now()->addDay());
            $file = Storage::get($webpFilename);

            return response($file, 200, [
                'Content-Type' => 'image/webp',
                'Cache-Control' => 'public, max-age=31536000',
                'Content-Length' => (string) strlen($file),
            ]);
        }

        $cachedSource = Cache::get($sourceCacheKey);
        if ($cachedSource === 'missing') {
            abort(404);
        }

        if (is_string($cachedSource) && str_starts_with($cachedSource, 'url:')) {
            return redirect()->away(substr($cachedSource, 4));
        }

        if ($cachedSource === 'file:webp' && Storage::exists($webpFilename)) {
            $file = Storage::get($webpFilename);

            return response($file, 200, [
                'Content-Type' => 'image/webp',
                'Cache-Control' => 'public, max-age=31536000',
                'Content-Length' => (string) strlen($file),
            ]);
        }

        $webpBlobColumn = "image_preview_{$slot}_webp";
        $urlColumn = "image_preview_{$slot}";

        $post = BuildPost::query()->whereKey($buildPostId)->first([$webpBlobColumn, $urlColumn]);
        if (! $post) {
            Cache::put($sourceCacheKey, 'missing', now()->addMinutes(10));
            abort(404);
        }

        $webpBlob = $post->{$webpBlobColumn};
        if (is_string($webpBlob) && $webpBlob !== '') {
            Storage::put($webpFilename, $webpBlob);
            Cache::put($sourceCacheKey, 'file:webp', now()->addDay());

            return response($webpBlob, 200, [
                'Content-Type' => 'image/webp',
                'Cache-Control' => 'public, max-age=31536000',
                'Content-Length' => (string) strlen($webpBlob),
            ]);
        }

        $url = $post->{$urlColumn};
        if (is_string($url) && $url !== '') {
            Cache::put($sourceCacheKey, "url:{$url}", now()->addHour());

            return redirect()->away($url);
        }

        Cache::put($sourceCacheKey, 'missing', now()->addMinutes(10));
        abort(404);
    }

    public function resolveBuildImageUrl(BuildPost $buildPost, int $slot): ?string
    {
        $urlColumn = "image_preview_{$slot}";
        $hasWebpBlobColumn = "has_image_preview_{$slot}_webp";

        $hasWebpBlob = (bool) ($buildPost->{$hasWebpBlobColumn} ?? false);
        $hasSourceUrl = is_string($buildPost->{$urlColumn}) && $buildPost->{$urlColumn} !== '';

        if (! $hasWebpBlob && ! $hasSourceUrl) {
            return null;
        }

        return route('builds.image', ['buildPost' => $buildPost->id, 'slot' => $slot]);
    }
}
