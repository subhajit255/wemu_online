<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtistProfile extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function primaryGenre()
    {
        return $this->belongsTo(Genre::class, 'primary_genre_id');
    }

    public function subGenre()
    {
        return $this->belongsTo(Genre::class, 'sub_genre_id');
    }
}
