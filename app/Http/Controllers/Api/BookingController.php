<?php
namespace App\Http\Controllers\Api;

use App\Models\Booking;

class BookingController extends CrudController
{
    protected string $model = Booking::class;

    protected function rules(bool $updating = false): array
    {
        $r = $this->required($updating);
        return ['user_id' => "$r|exists:users,id", 'court_id' => "$r|exists:courts,id", 'booking_date' => "$r|date", 'end_date' => 'sometimes|nullable|date|after_or_equal:booking_date', 'start_time' => "$r|date_format:H:i:s", 'end_time' => "$r|date_format:H:i:s|after:start_time", 'total_price' => "$r|numeric|min:0", 'payment_method' => 'sometimes|string|max:50', 'payment_receipt' => 'sometimes|nullable|string|max:2048', 'status' => 'sometimes|in:pending,confirmed,cancelled'];
    }
}
