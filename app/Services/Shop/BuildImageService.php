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

        $filename = "facebook_images/{$buildPostId}_preview_{$slot}.jpg";
        $sourceCacheKey = "build-image-source:{$buildPostId}:{$slot}";

        if (Storage::exists($filename)) {
            Cache::put($sourceCacheKey, 'file', now()->addDay());
            $file = Storage::get($filename);

            return response($file, 200, [
                'Content-Type' => 'image/jpeg',
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

        $blobColumn = "image_preview_{$slot}_blob";
        $urlColumn = "image_preview_{$slot}";

        $post = BuildPost::query()->whereKey($buildPostId)->first([$blobColumn, $urlColumn]);
        if (! $post) {
            Cache::put($sourceCacheKey, 'missing', now()->addMinutes(10));
            abort(404);
        }

        $blob = $post->{$blobColumn};
        if (is_string($blob) && $blob !== '') {
            Storage::put($filename, $blob);
            Cache::put($sourceCacheKey, 'file', now()->addDay());

            return response($blob, 200, [
                'Content-Type' => 'image/jpeg',
                'Cache-Control' => 'public, max-age=31536000',
                'Content-Length' => (string) strlen($blob),
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
        $hasBlobColumn = "has_image_preview_{$slot}_blob";

        $hasBlob = (bool) ($buildPost->{$hasBlobColumn} ?? false);
        $hasSourceUrl = is_string($buildPost->{$urlColumn}) && $buildPost->{$urlColumn} !== '';

        if (! $hasBlob && ! $hasSourceUrl) {
            return null;
        }

        return route('builds.image', ['buildPost' => $buildPost->id, 'slot' => $slot]);
    }
}
