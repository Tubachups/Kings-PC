<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_preview_1',
        'image_preview_2',
        'image_preview_3',
        'image_preview_4',
        'text',
        'likes',
    ];

    protected $casts = [
        'likes' => 'integer',
    ];
}
