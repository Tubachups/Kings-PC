<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacebookReview extends Model
{
    //
    protected $table = 'facebook_reviews';
    protected $fillable = [
        'Name', 'Feedback', 'web_sraper_order'];

    public $timestamps = false;
}
