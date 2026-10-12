<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserApiController extends Controller
{
    public function store(Request $request) {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->role = $request->role ?? 'athlete';
        $user->password = Hash::make($request->password ?? 'bmvl1234');
        $user->save();
        return response()->json($user, 201);
    }
    public function index() { return response()->json(User::all(), 200); }
    public function show($id) { return response()->json(User::findOrFail($id), 200); }
    public function update(Request $request, $id) {
        $user = User::findOrFail($id);
        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        $user->phone_number = $request->phone_number ?? $user->phone_number;
        $user->role = $request->role ?? $user->role;
        $user->save();
        return response()->json($user, 200);
    }
    public function delete($id) {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'User Removed'], 200);
    }
    
    // PHASE 3: One-to-Many Relationship (User -> Bookings)
    public function getUserBookings($id) {
        $user = User::where('id', $id)->with('bookings')->first();
        if (!$user) return response()->json(['message' => 'User not found'], 404);
        return response()->json(['data' => $user], 200);
    }
}