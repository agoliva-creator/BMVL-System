<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class EquipmentController extends Controller
{
    /**
     * Bouncer function to strictly lock actions to Admins.
     */
    private function requireAdmin()
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action. Only Admins can perform this task.');
        }
    }

    /**
     * Bouncer function to block Athletes (Staff & Admin can pass).
     */
    private function blockAthletes()
    {
        if (Auth::check() && Auth::user()->role === 'athlete') {
            abort(403, 'Unauthorized action. Athletes cannot manage equipment.');
        }
    }

    public function index()
    {
        // EVERYONE (Admin, Staff, and Athletes) can view the equipment list
        $equipment = Equipment::all();
        return view('equipment.index', compact('equipment'));
    }

    public function create()
    {
        $this->blockAthletes(); // Staff CAN add equipment
        return view('equipment.create');
    }

    public function store(Request $request)
    {
        $this->blockAthletes(); // Staff CAN store equipment

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:available,maintenance',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('equipment', 'public');
        }

        Equipment::create($validated);

        return redirect()->route('equipment.index')->with('success', 'Equipment added successfully!');
    }

    public function edit(string $id)
    {
        $this->blockAthletes(); // Staff CAN edit equipment
        $equipment = Equipment::findOrFail($id);
        return view('equipment.edit', compact('equipment'));
    }

    public function update(Request $request, string $id)
    {
        $this->blockAthletes(); // Staff CAN update equipment

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:available,maintenance',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $equipment = Equipment::findOrFail($id);

        if ($request->hasFile('image')) {
            if ($equipment->image) {
                Storage::disk('public')->delete($equipment->image);
            }
            $validated['image'] = $request->file('image')->store('equipment', 'public');
        }

        $equipment->update($validated);

        return redirect()->route('equipment.index')->with('success', 'Equipment updated successfully!');
    }

    public function destroy(string $id)
    {
        $this->requireAdmin(); // ONLY ADMINS can delete equipment

        $equipment = Equipment::findOrFail($id);

        if ($equipment->image) {
            Storage::disk('public')->delete($equipment->image);
        }

        $equipment->delete();

        return redirect()->route('equipment.index')->with('success', 'Equipment deleted successfully!');
    }
}