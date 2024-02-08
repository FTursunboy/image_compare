<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecalculatedImages extends Model
{
    use HasFactory;
    protected $guarded = false;


    public function setHashAttribute($value)
    {
        $this->attributes['hash'] = serialize($value);
    }

    public function getHashAttribute($value)
    {
        return unserialize($value);
    }
}
