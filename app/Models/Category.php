<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $fillable = ['name', 'slug'];

    // Define the relationship: A category has many products (one-to-many)
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
