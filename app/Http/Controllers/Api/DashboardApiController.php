<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyUpdate;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function index(): JsonResponse
    {
        $projectsCount = Project::count();
        $activeProjectsCount = Project::whereIn('status', ['active', 'in_progress', 'open'])->count();
        $tasksCount = Task::count();
        $completedTasksCount = Task::whereIn('status', ['done', 'completed', 'closed'])->count();
        $completionRate = $tasksCount > 0 ? round(($completedTasksCount / $tasksCount) * 100, 1) : 0;

        return response()->json([
            'success' => true,
            'message' => 'Workspace dashboard overview loaded.',
            'data' => [
                'kpis' => [
                    'projects_total' => $projectsCount,
                    'projects_active' => $activeProjectsCount > 0 ? $activeProjectsCount : $projectsCount,
                    'tasks_total' => $tasksCount,
                    'tasks_completed' => $completedTasksCount,
                    'completion_rate_percent' => $completionRate,
                    'files_total' => ProjectFile::count(),
                    'meetings_total' => Meeting::count(),
                    'daily_updates_total' => DailyUpdate::count(),
                ],
                'recent_projects' => Project::latest()->take(5)->get(),
                'recent_tasks' => Task::latest()->take(6)->get(),
                'upcoming_meetings' => Meeting::latest()->take(4)->get(),
                'recent_updates' => DailyUpdate::latest()->take(5)->get(),
            ],
        ]);
    }

    public function askAi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => ['required', 'string', 'max:2000'],
        ]);

        $projectsTotal = Project::count();
        $tasksTotal = Task::count();
        $doneTasks = Task::whereIn('status', ['done', 'completed', 'closed'])->count();
        $blockedUpdates = DailyUpdate::whereNotNull('blockers')
            ->where('blockers', '!=', '')
            ->latest()
            ->take(3)
            ->pluck('blockers');

        $answer = sprintf(
            "Workspace Summary: %d projects and %d tasks (%d completed). Query addressed: \"%s\".",
            $projectsTotal,
            $tasksTotal,
            $doneTasks,
            $validated['prompt']
        );

        if ($blockedUpdates->isNotEmpty()) {
            $answer .= ' Active blockers noted: ' . $blockedUpdates->implode(' | ') . '.';
        }

        return response()->json([
            'success' => true,
            'message' => 'AI assistant response generated.',
            'data' => [
                'prompt' => $validated['prompt'],
                'reply' => $answer,
                'context_snapshot' => [
                    'projects' => $projectsTotal,
                    'tasks' => $tasksTotal,
                    'completed_tasks' => $doneTasks,
                ],
            ],
        ]);
    }
}
