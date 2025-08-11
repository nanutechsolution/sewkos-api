<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomTypePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_type_id',
        'period_type',
        'price',
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}