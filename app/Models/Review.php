<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'kos_id',
        'author_name',
        'comment',
        'rating',
    ];
    public function kos()
    {
        return $this->belongsTo(Kos::class);
    }
}