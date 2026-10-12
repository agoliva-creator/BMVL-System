<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationWelcome;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'athlete',
        ]);

        $notificationSent = false;
        try {
            Mail::to($user->email)->send(new RegistrationWelcome($user));
            $notificationSent = true;
        } catch (Throwable) {
            Log::warning('Registration welcome email could not be sent.', ['user_id' => $user->id]);
        }

        return response()->json([
            'message' => 'Registration successful.',
            'data' => $user,
            'notification_sent' => $notificationSent,
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !Hash::check($credentials['password'], $user->password) || !in_array($user->role, ['admin', 'staff'], true)) {
            return response()->json(['message' => 'Invalid credentials or insufficient role.'], 401);
        }

        return response()->json(['token_type' => 'Bearer', 'token' => $user->createToken('thunder-client')->plainTextToken, 'user' => $user]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }
}
