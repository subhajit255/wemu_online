<?php

namespace App\Models;


use Webpatser\Uuid\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genre extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    public function parent()
    {
        return $this->belongsTo(Genre::class, 'parent_id');
    }
}
