<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit');
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        // 1. Detect if the user is attempting to change their email
        $isChangingEmail = $request->input('email') !== $user->email;

        // 2. Define the base rules (Demographics are always allowed to change easily)
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'gender' => 'nullable|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];

        // 3. BULLETPROOF SECURITY CHECK
        // If they are changing their email OR setting a new password, current_password becomes strictly required.
        if ($isChangingEmail || $request->filled('password')) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        // 4. Run the validation with custom error messages so the user knows why they were blocked
        $validated = $request->validate($rules, [
            'current_password.required' => 'For your security, you must provide your current password to change your email address or password.',
            'current_password.current_password' => 'The current password you provided is incorrect.'
        ]);

        // 5. Handle profile picture upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        // 6. Save the text updates
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        
        // Safely map optional demographic fields
        if (array_key_exists('phone_number', $validated)) $user->phone_number = $validated['phone_number'];
        if (array_key_exists('gender', $validated)) $user->gender = $validated['gender'];
        if (array_key_exists('date_of_birth', $validated)) $user->date_of_birth = $validated['date_of_birth'];
        if (array_key_exists('address', $validated)) $user->address = $validated['address'];

        // Only hash and save a new password if one was provided
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }
}