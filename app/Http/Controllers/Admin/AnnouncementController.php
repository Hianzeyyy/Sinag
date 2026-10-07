<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function guardAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index()
    {
        $this->guardAdmin();

        // Show only announcements created by the currently logged-in admin.
        $announcements = Announcement::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        $this->guardAdmin();

        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $this->guardAdmin();

        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        Announcement::create([
            'title' => $request->title,
            'body' => $request->body,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement posted!');
    }

    public function edit($id)
    {
        $this->guardAdmin();

        $announcement = Announcement::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, $id)
    {
        $this->guardAdmin();

        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $announcement = Announcement::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $announcement->update([
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated successfully.');
    }

    public function destroy($id)
    {
        $this->guardAdmin();

        $announcement = Announcement::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted successfully.');
    }
}
