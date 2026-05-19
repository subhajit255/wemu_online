<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayListSong extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function playlist()
    {
        return $this->belongsTo(PlayList::class);
    }
    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}
