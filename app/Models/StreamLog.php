<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StreamLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}
