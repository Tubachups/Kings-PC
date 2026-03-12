<?php

namespace App\Services\Shop;

use App\Models\BuildPost;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BuildFeedService
{
    public function __construct(private BuildImageService $buildImageService) {}

    public function getTopBuilds(int $limit = 6): Collection
    {
        return $this->baseBuildQuery()
            ->applySort('price')
            ->limit($limit)
            ->get()
            ->map(fn (BuildPost $buildPost): array => $this->mapBuildPost($buildPost))
            ->values();
    }

    public function getBuildFeed(string $sort, ?int $minPrice, ?int $maxPrice, int $perPage = 15): LengthAwarePaginator
    {
        return $this->baseBuildQuery()
            ->applySort($sort)
            ->applyPriceRange($minPrice, $maxPrice)
            ->paginate($perPage)
            ->through(fn (BuildPost $buildPost): array => $this->mapBuildPost($buildPost));
    }

    private function baseBuildQuery(): Builder
    {
        return BuildPost::query()
            ->select([
                'id',
                'text',
                'likes',
                'created_at',
                'image_preview_1',
                'image_preview_2',
                'image_preview_3',
                'image_preview_4',
            ])
            ->selectRaw('image_preview_1_webp IS NOT NULL as has_image_preview_1_webp')
            ->selectRaw('image_preview_2_webp IS NOT NULL as has_image_preview_2_webp')
            ->selectRaw('image_preview_3_webp IS NOT NULL as has_image_preview_3_webp')
            ->selectRaw('image_preview_4_webp IS NOT NULL as has_image_preview_4_webp')
            ->selectRaw('image_preview_4_webp IS NOT NULL as has_image_preview_4_webp');
    }

    private function mapBuildPost(BuildPost $buildPost): array
    {
        return [
            'id' => $buildPost->id,
            'text' => $buildPost->text,
            'likes' => $buildPost->likes,
            'created_at' => $buildPost->created_at,
            'image_preview_1' => $this->buildImageService->resolveBuildImageUrl($buildPost, 1),
            'image_preview_2' => $this->buildImageService->resolveBuildImageUrl($buildPost, 2),
            'image_preview_3' => $this->buildImageService->resolveBuildImageUrl($buildPost, 3),
            'image_preview_4' => $this->buildImageService->resolveBuildImageUrl($buildPost, 4),
        ];
    }
}
