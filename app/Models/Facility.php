<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
    ];

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'property_facilities');
    }

    public function roomTypes()
    {
        return $this->belongsToMany(RoomType::class, 'room_type_facilities');
    }
}