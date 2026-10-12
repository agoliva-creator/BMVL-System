<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\AdminUserCredentials;
use Throwable;

class UserController extends Controller
{
    /**
     * Bouncer function to block Athletes from everything, 
     * and block Staff from specific destructive actions.
     */
    private function requireAdmin()
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action. Only Admins can perform this task.');
        }
    }

    private function blockAthletes()
    {
        if (Auth::check() && Auth::user()->role === 'athlete') {
            abort(403, 'Unauthorized action. Athletes cannot manage users.');
        }
    }

    public function index()
    {
        // Both Admin and Staff can view the index list
        $this->blockAthletes();
        
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        // STRICT READ-ONLY: Staff are not allowed to create users
        $this->requireAdmin(); 
        
        return view('users.create');
    }

    public function store(Request $request)
    {
        // STRICT READ-ONLY: Staff are not allowed to store users
        $this->requireAdmin(); 

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'required|in:admin,staff,athlete',
        ]);

        // Auto-generated rather than admin-typed
        $generatedPassword = Str::random(10);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($generatedPassword),
        ]);

        try {
            Mail::to($user->email)->send(new AdminUserCredentials($user, $generatedPassword));
        } catch (Throwable $exception) {
            // Account creation should succeed even when the mail server is unavailable.
            Log::warning('Admin-created user credentials email could not be sent.', [
                'user_id' => $user->id,
                'exception' => $exception::class,
            ]);

            return redirect()->route('users.index')
                ->with('error', 'The user was created, but the temporary password email could not be sent. Check the mail configuration and share the one-time password below securely.')
                ->with('generated_password', $generatedPassword)
                ->with('generated_email', $user->email);
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully. A temporary password was sent to ' . $user->email . '.');
    }

    public function edit(string $id)
    {
        // STRICT READ-ONLY: Staff are not allowed to edit users
        $this->requireAdmin(); 
        
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, string $id)
    {
        // STRICT READ-ONLY: Staff are not allowed to update users
        $this->requireAdmin(); 

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:admin,staff,athlete',
            'phone_number' => 'nullable|string|max:30',
            'gender' => 'nullable|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:2000',
        ]);

        $user = User::findOrFail($id);

        // Prevent an admin from accidentally demoting their own account
        if (auth()->id() == $user->id && $validated['role'] !== 'admin') {
            return redirect()->route('users.index')
                ->with('error', 'You cannot change your own role away from Admin.');
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    public function destroy(string $id)
    {
        // STRICT READ-ONLY: Staff are not allowed to delete users
        $this->requireAdmin(); 

        $user = User::findOrFail($id);

        // Prevent the admin from deleting themselves
        if (auth()->id() == $user->id) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }
}
