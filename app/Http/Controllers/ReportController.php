<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Report;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $projectId = $request->get('project_id');

        $tasksQuery = Task::query();
        if ($projectId) {
            $tasksQuery->where('project_id', $projectId);
        }

        $totalTasks = (clone $tasksQuery)->count();
        $completedTasks = (clone $tasksQuery)->where('status', 'completed')->count();
        $taskCompletionRate = $totalTasks > 0 ? (int) round(($completedTasks / $totalTasks) * 100) : 0;

        $projects = Project::with('tasks')->orderBy('name')->get();
        $totalProjects = $projects->count();
        $completedProjects = $projects->filter(fn ($p) => ($p->completion_percentage ?? 0) === 100)->count();
        $projectCompletionRate = $totalProjects > 0 ? (int) round(($completedProjects / $totalProjects) * 100) : 0;

        $totalUsers = User::count();
        $activeUsers = User::where('availability_status', 'available')->count();
        $teamActivityRate = $totalUsers > 0 ? (int) round(($activeUsers / $totalUsers) * 100) : 0;

        $reportsQuery = Report::with(['project', 'submitter'])->latest();
        if ($projectId) {
            $reportsQuery->where('project_id', $projectId);
        }
        $reports = $reportsQuery->get();

        return view('reports.index', compact(
            'projects',
            'projectId',
            'taskCompletionRate',
            'projectCompletionRate',
            'teamActivityRate',
            'reports'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'report_type' => ['required', 'string', 'in:Project Progress,Meetings Summary,Worker Performance,Tasks,KPIs'],
            'note' => ['nullable', 'string', 'max:2000'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        Report::create([
            'project_id' => $validated['project_id'] ?? null,
            'submitted_by' => $request->user()->id,
            'report_type' => $validated['report_type'],
            'note' => $validated['note'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
        ]);

        return back()->with('success', 'Detailed report submitted successfully.');
    }

    public function export(Request $request): StreamedResponse
    {
        $reports = Report::with(['project', 'submitter'])->latest()->get();

        return response()->streamDownload(function () use ($reports) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Type', 'Project', 'Submitted By', 'Start Date', 'End Date', 'Note', 'Created At']);

            foreach ($reports as $report) {
                fputcsv($handle, [
                    $report->id,
                    $report->report_type,
                    $report->project?->name ?? 'All Projects',
                    $report->submitter?->name ?? 'System',
                    $report->start_date?->format('Y-m-d') ?? '',
                    $report->end_date?->format('Y-m-d') ?? '',
                    $report->note ?? '',
                    $report->created_at?->toDateTimeString() ?? '',
                ]);
            }

            fclose($handle);
        }, 'collabspace-reports.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}