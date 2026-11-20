<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $period = $request->get('period', 'this_month');
        $projectId = $request->get('project_id');
        $priorityFilter = $request->get('priority');

        $startDate = match ($period) {
            'today' => Carbon::today(),
            'this_week' => Carbon::now()->startOfWeek(),
            'last_three_month' => Carbon::now()->subMonths(3)->startOfDay(),
            'this_year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };

        $projects = Project::with('tasks')->latest()->get();

        $tasksQuery = Task::with(['project', 'assignees'])
            ->where('created_at', '>=', $startDate);

        if ($projectId) {
            $tasksQuery->where('project_id', $projectId);
        }

        $filteredTasks = (clone $tasksQuery)->get();

        $pendingCount = $filteredTasks->where('status', 'todo')->count();
        $inProgressCount = $filteredTasks->where('status', 'in_progress')->count();
        $completedCount = $filteredTasks->where('status', 'completed')->count();
        $totalCount = $filteredTasks->count();

        $completionRate = $totalCount > 0
            ? (int) round(($completedCount / $totalCount) * 100)
            : 0;

        $upcomingTasksQuery = Task::with(['project', 'assignees'])
            ->where('status', '!=', 'completed')
            ->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END")
            ->orderBy('due_date');

        if ($projectId) {
            $upcomingTasksQuery->where('project_id', $projectId);
        }

        if ($priorityFilter && in_array($priorityFilter, ['high', 'medium', 'low'], true)) {
            $upcomingTasksQuery->where('priority', $priorityFilter);
        }

        $upcomingTasks = $upcomingTasksQuery->take(6)->get();

        $teamsStatus = User::withCount([
            'tasks',
            'tasks as completed_tasks_count' => fn ($q) => $q->where('status', 'completed'),
        ])->take(6)->get();

        $upcomingMeetings = Meeting::with('project')
            ->where('end_at', '>=', now())
            ->orderBy('start_at')
            ->take(4)
            ->get();

        $recentFiles = ProjectFile::with(['project', 'uploader'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'period',
            'projectId',
            'priorityFilter',
            'projects',
            'pendingCount',
            'inProgressCount',
            'completedCount',
            'completionRate',
            'upcomingTasks',
            'teamsStatus',
            'upcomingMeetings',
            'recentFiles'
        ));
    }

    public function askAi(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'prompt' => ['required', 'string', 'max:500'],
        ]);

        $openTasksCount = Task::where('status', '!=', 'completed')->count();
        $activeProjectsCount = Project::where('status', 'active')->count();

        $reply = sprintf(
            'Summary for "%s": There are currently %d active projects and %d open tasks across workspaces.',
            $validated['prompt'],
            $activeProjectsCount,
            $openTasksCount
        );

        return back()->with('ai_reply', $reply);
    }
}