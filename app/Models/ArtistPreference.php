<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtistPreference extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'favorite_genres' => 'array',
    ];
}
