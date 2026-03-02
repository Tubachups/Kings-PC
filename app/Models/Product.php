<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'specs',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'specs' => 'array',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public static function checkoutStock($item, $quantity)
    {
        return self::where('id', $item)->decrement('stock', $quantity);
    }

    /**
     * Scope to filter products by name.
     */
    public function scopeFilterByName(Builder $query, ?string $name): Builder
    {
        if ($name) {
            return $query->where('name', 'like', "%{$name}%");
        }

        return $query;
    }

    /**
     * Scope to filter products by category name.
     */
    public function scopeFilterByCategory(Builder $query, ?string $category): Builder
    {
        if ($category) {
            return $query->whereHas('category', fn (Builder $q) => $q->where('name', 'like', "%{$category}%"));
        }

        return $query;
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['name'] ?? null, function ($q, $name) {
            $q->where('name', 'like', "%{$name}%");
        })->when($filters['category'] ?? null, function ($q, $category) {
            $q->whereHas('category', fn ($cq) => $cq->where('name', 'like', "%{$category}%"));
        });
    }
}
