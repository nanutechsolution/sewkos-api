<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kos extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'location',
        'price',
        'description',
        'image_url',
        'facilities',
        'status',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'facilities' => 'array',
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}