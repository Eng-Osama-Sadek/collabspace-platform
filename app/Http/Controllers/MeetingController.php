<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Meeting;
use App\Models\Notification;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MeetingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Meeting::with(['project', 'creator'])->latest('start_at');

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->get('project_id'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('agenda', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $meetings = $query->get();
        $projects = Project::orderBy('name')->get();

        return view('meetings.index', compact('meetings', 'projects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'agenda' => ['nullable', 'string', 'max:2000'],
            'meeting_link' => ['nullable', 'url', 'max:500'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after_or_equal:start_at'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $meeting = Meeting::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'project_id' => $meeting->project_id,
            'action' => 'scheduled_meeting',
            'description' => "Scheduled meeting: {$meeting->title}",
        ]);

        Notification::create([
            'user_id' => auth()->id(),
            'type' => 'meeting',
            'title' => 'New Meeting Scheduled',
            'body' => "Meeting \"{$meeting->title}\" has been scheduled.",
            'action_url' => route('meetings.show', $meeting),
        ]);

        return back()->with('success', 'Meeting scheduled successfully.');
    }

    public function destroy(Meeting $meeting): RedirectResponse
    {
        $title = $meeting->title;
        $meeting->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'deleted_meeting',
            'description' => "Deleted meeting: {$title}",
        ]);

        return back()->with('success', 'Meeting removed.');
    }
}