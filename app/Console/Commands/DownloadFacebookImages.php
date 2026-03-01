<?php

namespace App\Console\Commands;

use App\Models\BuildPost;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DownloadFacebookImages extends Command
{
    protected $signature = 'images:download';

    protected $description = 'Download Facebook CDN images, save JPG files, and persist blobs';

    public function handle(): int
    {
        $posts = BuildPost::query()->get();
        $columns = [
            'image_preview_1',
            'image_preview_2',
            'image_preview_3',
            'image_preview_4',
        ];

        foreach ($posts as $post) {
            foreach ($columns as $index => $column) {
                $url = $post->{$column};
                if (! is_string($url) || $url === '') {
                    continue;
                }

                $blobColumn = "{$column}_blob";
                if (! is_null($post->{$blobColumn})) {
                    $this->line("Skipped [{$post->id}] {$blobColumn} already exists");

                    continue;
                }

                $filename = "facebook_images/{$post->id}_preview_".($index + 1).'.jpg';

                if (Storage::exists($filename)) {
                    $post->{$blobColumn} = Storage::get($filename);
                    $post->save();
                    $this->line("Synced existing file to DB: {$filename}");

                    continue;
                }

                try {
                    $response = Http::withHeaders([
                        'User-Agent' => 'Mozilla/5.0',
                    ])->timeout(20)->get($url);

                    if (! $response->successful()) {
                        $this->error("Failed [{$post->id}] {$column}: HTTP {$response->status()}");

                        continue;
                    }

                    $imageData = $response->body();
                    if ($imageData === '') {
                        $this->error("Failed [{$post->id}] {$column}: empty response body");

                        continue;
                    }

                    Storage::put($filename, $imageData);
                    $post->{$blobColumn} = $imageData;
                    $post->save();
                    $this->info("Saved and updated row {$post->id}: {$filename}");
                } catch (Throwable $exception) {
                    $this->error("Failed [{$post->id}] {$column}: {$exception->getMessage()}");
                }
            }
        }

        $this->info('Done');

        return self::SUCCESS;
    }
}
