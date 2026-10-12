<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    // This tells Laravel these columns are safe to insert data into
    protected $fillable = [
        'name',
        'price',
        'stock',
        'status',
        'image' // Adding this now to prepare for your next question!
    ];

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_equipment')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }
}