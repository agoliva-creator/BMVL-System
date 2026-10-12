<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipment;

class EquipmentApiController extends Controller
{
    public function store(Request $request) {
        $equipment = new Equipment();
        $equipment->name = $request->name;
        $equipment->price = $request->price;
        $equipment->stock = $request->stock;
        $equipment->save();
        return response()->json($equipment, 201);
    }
    public function index() { return response()->json(Equipment::all(), 200); }
    public function show($id) { return response()->json(Equipment::findOrFail($id), 200); }
    public function update(Request $request, $id) {
        $equipment = Equipment::findOrFail($id);
        $equipment->name = $request->name ?? $equipment->name;
        $equipment->price = $request->price ?? $equipment->price;
        $equipment->stock = $request->stock ?? $equipment->stock;
        $equipment->save();
        return response()->json($equipment, 200);
    }
    public function delete($id) {
        Equipment::findOrFail($id)->delete();
        return response()->json(['message' => 'Equipment Removed'], 200);
    }
}