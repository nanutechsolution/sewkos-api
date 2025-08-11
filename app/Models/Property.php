<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'gender_preference',
        'description',
        'rules',
        'rules_file_url',
        'year_built',
        'manager_name',
        'manager_phone',
        'notes',
        'address_street',
        'address_city',
        'address_province',
        'address_zip_code',
        'latitude',
        'longitude',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function roomTypes()
    {
        return $this->hasMany(RoomType::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'property_id');
    }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'property_facilities', 'property_id', 'facility_id');
    }
}