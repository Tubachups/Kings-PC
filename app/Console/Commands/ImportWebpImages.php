<?php

namespace App\Console\Commands;

use App\Models\BuildPost;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportWebpImages extends Command
{
    protected $signature = 'images:import-webp';

    protected $description = 'Import local WebP images from storage into the image_preview_x_webp columns of build_posts';

    public function handle(): int
    {
        $directory = storage_path('app/private/facebook_images');

        if (! File::isDirectory($directory)) {
            $this->error('Directory not found: '.$directory);

            return self::FAILURE;
        }

        $webpFiles = File::glob($directory.'/*.webp');

        if (empty($webpFiles)) {
            $this->warn('No WebP files found in '.$directory);

            return self::SUCCESS;
        }

        $this->info('Found '.count($webpFiles).' WebP files. Importing...');
        $bar = $this->output->createProgressBar(count($webpFiles));
        $bar->start();

        $updated = 0;
        $skipped = 0;

        foreach ($webpFiles as $filePath) {
            $basename = basename($filePath, '.webp'); // e.g. "10_preview_1"

            if (! preg_match('/^(\d+)_preview_([1-4])$/', $basename, $matches)) {
                $this->newLine();
                $this->warn("Skipping unrecognised filename: {$basename}.webp");
                $skipped++;
                $bar->advance();

                continue;
            }

            $postId = (int) $matches[1];
            $previewNumber = (int) $matches[2];
            $column = "image_preview_{$previewNumber}_webp";

            $post = BuildPost::query()->find($postId);

            if ($post === null) {
                $skipped++;
                $bar->advance();

                continue;
            }

            $post->{$column} = File::get($filePath);
            $post->save();
            $updated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done. Updated: {$updated}, Skipped: {$skipped}.");

        return self::SUCCESS;
    }
}
