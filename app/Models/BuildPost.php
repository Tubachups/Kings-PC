<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_preview_1',
        'image_preview_1_blob',
        'image_preview_1_webp',
        'image_preview_2',
        'image_preview_2_blob',
        'image_preview_2_webp',
        'image_preview_3',
        'image_preview_3_blob',
        'image_preview_3_webp',
        'image_preview_4',
        'image_preview_4_blob',
        'image_preview_4_webp',
        'text',
        'likes',
    ];

    protected $casts = [
        'likes' => 'integer',
    ];

    private static function priceExpression(): string
    {
        return "
            (CASE
                WHEN `text` REGEXP '[Uu]nit [Pp]rice[^0-9]*[0-9.]+[kK]'
                THEN CAST(REGEXP_SUBSTR(REGEXP_SUBSTR(`text`, '[Uu]nit [Pp]rice[^0-9]*[0-9.]+'), '[0-9.]+') AS DECIMAL(10,2)) * 1000
                ELSE CAST(REPLACE(
                    REGEXP_SUBSTR(
                        REGEXP_SUBSTR(`text`, '[Uu]nit [Pp]rice[^0-9]*[0-9,]+'),
                        '[0-9,]+'
                    ), ',', ''
                ) AS UNSIGNED)
            END)
        ";
    }

    public function scopeApplyPriceRange(Builder $query, ?int $minPrice = null, ?int $maxPrice = null): Builder
    {
        if ($minPrice === null && $maxPrice === null) {
            return $query;
        }

        $expr = self::priceExpression();

        if ($minPrice !== null) {
            $query->whereRaw("{$expr} >= ?", [$minPrice]);
        }

        if ($maxPrice !== null) {
            $query->whereRaw("{$expr} <= ?", [$maxPrice]);
        }

        return $query;
    }

    public function scopeApplySort(Builder $query, string $sort = 'newest'): Builder
    {
        return match ($sort) {
            'oldest' => $query->orderBy('created_at', 'asc'),
            'likes' => $query->orderByDesc('likes'),
            'price' => $query->orderByRaw(self::priceExpression().' DESC'),
            'price_low' => $query->orderByRaw(self::priceExpression().' ASC'),
            default => $query->orderByDesc('created_at'),
        };
    }
}
