<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    // 1. Allow mass assignment for these specific fields (Security measure)
    protected $fillable = [
        'user_id',
        'court_id',
        'booking_date',
        'end_date',
        'start_time',
        'end_time',
        'total_price',
        'payment_method',
        'payment_receipt',
        'status',
        'is_completed',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    // 2. Define the relationship: A Booking belongs to one specific User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 3. Define the relationship: A Booking belongs to one specific Court
    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function equipment()
    {
        return $this->belongsToMany(Equipment::class, 'booking_equipment')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }
}
