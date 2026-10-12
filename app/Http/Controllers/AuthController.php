<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationWelcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Throwable;

class AuthController extends Controller
{
    // 1. Show the Login Form
    public function showLogin()
    {
        return view('auth.login');
    }

    // 2. Process the Login Attempt
    public function login(Request $request)
    {
        // Validate the form inputs
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Attempt to log the user in using Laravel's Auth facade
        if (Auth::attempt($credentials)) {
            // Regenerate session to protect against session fixation attacks (Great defense point!)
            $request->session()->regenerate();

            $isAdmin = Auth::user()->role === 'admin';
            $redirectUrl = $isAdmin ? route('dashboard') : route('bookings.index');
            $message = $isAdmin ? 'Welcome back, Admin!' : 'Welcome back to the Player Portal!';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'redirect_url' => $redirectUrl,
                ]);
            }

            return redirect($redirectUrl)->with('success', $message);
        }

        // If login fails, send them back with a red error message
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'The provided credentials do not match our records.',
                'errors' => [
                    'email' => ['The provided credentials do not match our records.'],
                ],
            ], 422);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // 3. Process the Logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out.');
    }

    // Show the registration form
    public function showRegister()
    {
        return view('auth.register');
    }

    // Handle the registration logic
    public function register(Request $request)
    {
        // 1. Validate the user input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 2. Create the user and default their role to 'athlete'
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password), // Securely hash the password
            'role' => 'athlete', // Automatically assign the athlete role
        ]);

        $notificationSent = false;
        try {
            Mail::to($user->email)->send(new RegistrationWelcome($user));
            $notificationSent = true;
        } catch (Throwable) {
            Log::warning('Registration welcome email could not be sent.', ['user_id' => $user->id]);
        }

        // 3. Log the user in immediately after registering
        Auth::login($user);
        $request->session()->regenerate();

        $redirectUrl = Auth::user()->role === 'admin'
            ? route('dashboard')
            : route('bookings.index');
        $message = $notificationSent
            ? 'Registration successful! A welcome email was sent to your email address.'
            : 'Registration successful, but the welcome email could not be sent. Please check the mail settings.';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'redirect_url' => $redirectUrl,
                'notification_sent' => $notificationSent,
            ], 201);
        }

        return redirect($redirectUrl)->with('success', $message);
    }
}
