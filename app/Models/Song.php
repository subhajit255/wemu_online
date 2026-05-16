<?php

namespace App\Models;

use Webpatser\Uuid\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
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

    public function artist()
    {
        return $this->belongsTo(User::class);
    }
    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
    public function album()
    {
        return $this->belongsTo(Album::class);
    }
    public function getCoverImagePathAttribute()
    {
        $filePath = 'storage/song/images/' . $this->cover_image;
        if (!$this->cover_image || !file_exists(public_path($filePath))) {
            return asset('assets/media/books/11.png');
        }
        return asset($filePath);
    }
    public function getAudioPathAttribute()
    {
        $filePath = 'storage/song/audio/' . $this->audio_file;
        if (!$this->audio_file || !file_exists(public_path($filePath))) {
            return "#";
        }
        return asset($filePath);
    }
    public function getBackgroundPathAttribute()
    {
        $filePath = 'storage/song/images/' . $this->background;
        if (!$this->background || !file_exists(public_path($filePath))) {
            return asset('assets/media/books/11.png');
        }
        return asset($filePath);
    }
}
