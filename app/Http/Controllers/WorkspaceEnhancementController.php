<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DailyUpdate;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkspaceEnhancementController extends Controller
{
    public function dailyUpdates(Request $request): View
    {
        $projectId = $request->get('project_id');
        $projects = Project::orderBy('name')->get();

        $query = DailyUpdate::with(['user', 'project'])->latest('update_date')->latest();
        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        $updates = $query->get();

        return view('updates.index', compact('updates', 'projects', 'projectId'));
    }

    public function storeDailyUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'update_date' => ['required', 'date'],
            'completed_today' => ['required', 'string', 'max:1000'],
            'planned_next' => ['required', 'string', 'max:1000'],
            'blockers' => ['nullable', 'string', 'max:1000'],
        ]);

        $update = DailyUpdate::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'project_id' => $validated['project_id'] ?? null,
                'update_date' => $validated['update_date'],
            ],
            [
                'completed_today' => $validated['completed_today'],
                'planned_next' => $validated['planned_next'],
                'blockers' => $validated['blockers'] ?? null,
            ]
        );

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'project_id' => $update->project_id,
            'action_text' => 'Submitted daily status update for ' . $update->update_date->format('M d, Y'),
        ]);

        return back()->with('success', 'Daily status update saved.');
    }

    public function showMeeting(Request $request, Meeting $meeting): View
    {
        $meeting->load(['project', 'creator', 'attendees']);
        $search = $request->get('q');

        $lines = collect(preg_split('/\r\n|\r|\n/', (string) $meeting->transcript))
            ->filter(fn ($line) => trim($line) !== '')
            ->when($search, fn ($col) => $col->filter(fn ($line) => stripos($line, $search) !== false))
            ->values();

        return view('meetings.show', compact('meeting', 'lines', 'search'));
    }

    public function generateMeetingSummary(Request $request, Meeting $meeting): RedirectResponse
    {
        $transcriptText = trim((string) $meeting->transcript);
        $baseText = $transcriptText !== '' ? $transcriptText : ($meeting->agenda ?? $meeting->title);

        $summary = 'AI Summary: ' . mb_substr(preg_replace('/\s+/', ' ', $baseText), 0, 260) . '...';
        $keyPoints = implode("\n", [
            '• Reviewed meeting agenda and current project milestones.',
            '• Identified UX/technical feedback and action items.',
            '• Confirmed follow-up ownership and next review timing.',
        ]);

        $meeting->update([
            'ai_summary' => $summary,
            'key_points' => $keyPoints,
        ]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'project_id' => $meeting->project_id,
            'action_text' => 'Generated AI summary for meeting: ' . $meeting->title,
        ]);

        return back()->with('success', 'AI summary and key points generated.');
    }

    public function memberProfile(User $user): View
    {
        $user->load([
            'tasks.project',
            'uploadedFiles.project',
            'activityLogs' => fn ($q) => $q->latest()->take(10),
        ]);

        $todoTasks = $user->tasks->where('status', '!=', 'completed');
        $doneTasks = $user->tasks->where('status', 'completed');

        return view('members.show', compact('user', 'todoTasks', 'doneTasks'));
    }

    public function updateProjectWiki(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'wiki_notes' => ['nullable', 'string', 'max:5000'],
            'project_type' => ['nullable', 'string', 'max:80'],
        ]);

        $project->update([
            'wiki_notes' => $validated['wiki_notes'] ?? null,
            'project_type' => $validated['project_type'] ?? $project->project_type,
        ]);

        return back()->with('success', 'Project documentation & details updated.');
    }
}