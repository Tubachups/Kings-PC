<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

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

    // This automatically converts the JSON from the database into a PHP array,
    // and turns it back into JSON when saving.
    protected $casts = [
        'specs' => 'array',
        'is_active' => 'boolean',
    ];

    // Define the relationship: A product belongs to a category (many-to-one)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
