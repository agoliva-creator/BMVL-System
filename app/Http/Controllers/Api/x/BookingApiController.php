<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class BookingApiController extends Controller
{
    public function store(Request $request) {
        $booking = new Booking();
        $booking->user_id = $request->user_id;
        $booking->court_id = $request->court_id;
        $booking->booking_date = $request->booking_date;
        $booking->start_time = $request->start_time;
        $booking->end_time = $request->end_time;
        $booking->total_price = $request->total_price;
        $booking->status = $request->status ?? 'pending';
        $booking->save();
        return response()->json($booking, 201);
    }
    public function index() { return response()->json(Booking::with(['user', 'court'])->get(), 200); }
    public function show($id) { return response()->json(Booking::with(['user', 'court'])->findOrFail($id), 200); }
    public function update(Request $request, $id) {
        $booking = Booking::findOrFail($id);
        $booking->booking_date = $request->booking_date ?? $booking->booking_date;
        $booking->start_time = $request->start_time ?? $booking->start_time;
        $booking->end_time = $request->end_time ?? $booking->end_time;
        $booking->status = $request->status ?? $booking->status;
        $booking->save();
        return response()->json($booking, 200);
    }
    public function delete($id) {
        Booking::findOrFail($id)->delete();
        return response()->json(['message' => 'Booking Removed'], 200);
    }
}