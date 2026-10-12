<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Court;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CourtController extends Controller
{
    /**
     * Bouncer function to block Staff and Athletes from specific destructive actions.
     */
    private function requireAdmin()
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action. Only Admins can perform this task.');
        }
    }

    public function index()
    {
        // EVERYONE (Admin, Staff, and Athletes) can view the courts list
        $courts = Court::all();
        return view('courts.index', compact('courts'));
    }

    public function create()
    {
        $this->requireAdmin(); // Only Admins can add courts
        return view('courts.create');
    }

    public function store(Request $request)
    {
        $this->requireAdmin(); // Only Admins can add courts

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sport_type' => 'required|string|max:255',
            'hourly_rate' => 'required|numeric|min:0',
            'status' => 'required|in:available,maintenance',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('courts', 'public');
        }

        Court::create($validated);
        return redirect()->route('courts.index')->with('success', 'New court added successfully!');
    }

    public function show(string $id)
    {
        $court = Court::findOrFail($id);
        return view('courts.show', compact('court'));
    }

    public function edit(string $id)
    {
        // STRICT READ-ONLY: Staff are no longer allowed to edit courts
        $this->requireAdmin(); 
        
        $court = Court::findOrFail($id);
        return view('courts.edit', compact('court'));
    }

    public function update(Request $request, string $id)
    {
        // STRICT READ-ONLY: Staff are no longer allowed to update courts
        $this->requireAdmin(); 

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sport_type' => 'required|string|max:255',
            'hourly_rate' => 'required|numeric|min:0',
            'status' => 'required|in:available,maintenance',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $court = Court::findOrFail($id);

        if ($request->hasFile('image')) {
            if ($court->image) {
                Storage::disk('public')->delete($court->image);
            }
            $validated['image'] = $request->file('image')->store('courts', 'public');
        }

        $court->update($validated);
        return redirect()->route('courts.index')->with('success', 'Court updated successfully!');
    }

    public function destroy(string $id)
    {
        $this->requireAdmin(); // Only Admins can delete courts

        $court = Court::findOrFail($id);
        
        if ($court->image) {
            Storage::disk('public')->delete($court->image);
        }
        
        $court->delete();
        return redirect()->route('courts.index')->with('success', 'Court deleted successfully!');
    }
}