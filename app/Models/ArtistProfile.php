<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtistProfile extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function getImagePathAttribute()
    {
        $filePath = 'storage/profile/' . $this->profile_image;
        if (!$this->profile_image || !file_exists(public_path($filePath))) {
            return asset('assets/media/avatars/blank.png');
        }
        return asset($filePath);
    }
    public function getCoverImagePathAttribute()
    {
        $filePath = 'storage/banner/' . $this->cover_banner;
        if (!$this->cover_banner || !file_exists(public_path($filePath))) {
            return asset('assets/media/books/11.png');
        }
        return asset($filePath);
    }

    public function primaryGenre()
    {
        return $this->belongsTo(Genre::class, 'primary_genre_id');
    }

    public function subGenre()
    {
        return $this->belongsTo(Genre::class, 'sub_genre_id');
    }
}
