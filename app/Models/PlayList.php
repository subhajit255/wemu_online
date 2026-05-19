<?php

namespace App\Models;

use Webpatser\Uuid\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayList extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Uuid::generate(4);
        });
    }

    public function getCoverImagePathAttribute()
    {
        $filePath = 'storage/' . config('constants.SITE_PLAYLIST_COVER_IMAGE_UPLOAD_PATH') . $this->cover_image;
        if (!$this->cover_image || !file_exists(public_path($filePath))) {
            return asset('assets/media/books/11.png');
        }
        return asset($filePath);
    }

    public function songs()
    {
        return $this->belongsToMany(Song::class, 'play_list_songs', 'play_list_id', 'song_id')->withTimestamps();
    }
}
