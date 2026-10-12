<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
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
            abort(403, 'Unauthorized action. Athletes cannot manage announcements.');
        }
    }

    public function index()
    {
        // Start the query with the pinning and sorting logic
        $query = Announcement::orderByDesc('pinned')->orderBy('id', 'asc');

        // ACCESS CONTROL: If the user is an Athlete, filter out hidden and staff-only posts
        if (Auth::user()->role === 'athlete') {
            $query->where('status', 'active')
                  ->whereIn('target_audience', ['all', 'athlete']);
        }

        $announcements = $query->get();

        return view('announcements.index', compact('announcements'));
    }

    public function create()
    {
        $this->blockAthletes();
        return view('announcements.create');
    }

    public function store(Request $request)
    {
        $this->blockAthletes();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:active,hidden',
            'target_audience' => 'required|in:all,athlete,staff',
        ]);

        // Checkboxes don't submit at all when unchecked, so this has to be
        // set explicitly rather than pulled from validated().
        $validated['pinned'] = $request->has('pinned');

        Announcement::create($validated);

        return redirect()->route('announcements.index')->with('success', 'Announcement posted successfully!');
    }

    public function edit(string $id)
    {
        $this->blockAthletes();
        $announcement = Announcement::findOrFail($id);
        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, string $id)
    {
        $this->blockAthletes();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:active,hidden',
            'target_audience' => 'required|in:all,athlete,staff',
        ]);

        $validated['pinned'] = $request->has('pinned');

        $announcement = Announcement::findOrFail($id);
        $announcement->update($validated);

        return redirect()->route('announcements.index')->with('success', 'Announcement updated successfully!');
    }

    public function destroy(string $id)
    {
        $this->requireAdmin(); // ONLY ADMINS can delete announcements

        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return redirect()->route('announcements.index')->with('success', 'Announcement deleted successfully!');
    }
}