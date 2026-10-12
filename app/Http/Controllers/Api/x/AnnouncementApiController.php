<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;

class AnnouncementApiController extends Controller
{
    public function store(Request $request) {
        $announcement = new Announcement();
        $announcement->title = $request->title;
        $announcement->content = $request->content;
        $announcement->save();
        return response()->json($announcement, 201);
    }
    public function index() { return response()->json(Announcement::all(), 200); }
    public function show($id) { return response()->json(Announcement::findOrFail($id), 200); }
    public function update(Request $request, $id) {
        $announcement = Announcement::findOrFail($id);
        $announcement->title = $request->title ?? $announcement->title;
        $announcement->content = $request->content ?? $announcement->content;
        $announcement->save();
        return response()->json($announcement, 200);
    }
    public function delete($id) {
        Announcement::findOrFail($id)->delete();
        return response()->json(['message' => 'Announcement Removed'], 200);
    }
}