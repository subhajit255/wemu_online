<?php

namespace App\Models;

use Webpatser\Uuid\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Album extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Uuid::generate(4);
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
    public function songs()
    {
        return $this->hasMany(Song::class);
    }
    public function getImagePathAttribute()
    {
        $filePath = 'storage/album/' . $this->cover_image;
        if (!$this->cover_image || !file_exists(public_path($filePath))) {
            return asset('assets/media/books/11.png');
        }
        return asset($filePath);
    }
}
