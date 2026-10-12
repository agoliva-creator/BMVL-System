<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Court;

class CourtApiController extends Controller
{
    public function store(Request $request) {
        $court = new Court();
        $court->name = $request->name;
        $court->sport_type = $request->sport_type;
        $court->hourly_rate = $request->hourly_rate;
        $court->status = $request->status ?? 'available';
        $court->save();
        return response()->json($court, 201);
    }
    public function index() { return response()->json(Court::all(), 200); }
    public function show($id) { return response()->json(Court::findOrFail($id), 200); }
    public function update(Request $request, $id) {
        $court = Court::findOrFail($id);
        $court->name = $request->name ?? $court->name;
        $court->sport_type = $request->sport_type ?? $court->sport_type;
        $court->hourly_rate = $request->hourly_rate ?? $court->hourly_rate;
        $court->status = $request->status ?? $court->status;
        $court->save();
        return response()->json($court, 200);
    }
    public function delete($id) {
        Court::findOrFail($id)->delete();
        return response()->json(['message' => 'Court Removed'], 200);
    }
    
    // PHASE 3: One-to-Many Relationship (Court -> Bookings)
    public function getCourtBookings($id) {
        $court = Court::where('id', $id)->with('bookings')->first();
        if (!$court) return response()->json(['message' => 'Court not found'], 404);
        return response()->json(['data' => $court], 200);
    }
}