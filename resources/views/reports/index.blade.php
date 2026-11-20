@extends('layouts.app')

@section('title', 'Reports')
@section('header', 'Reports & Analytics')

@section('content')
<!-- Project Filter -->
<form method="GET" action="{{ route('reports.index') }}" class="bg-white p-4 rounded-xl border border-slate-200 mb-6 flex items-center gap-3">
    <select name="project_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <option value="">All Projects</option>
        @foreach($projects as $project)
            <option value="{{ $project->id }}" @selected((string)$projectId === (string)$project->id)>
                {{ $project->name }}
            </option>
        @endforeach
    </select>
    <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-medium">Filter</button>
</form>

<!-- Rate Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="text-xs text-slate-500">Task Completion Rate</div>
        <div class="text-2xl font-bold text-indigo-600 mt-1">{{ $taskCompletionRate }}%</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="text-xs text-slate-500">Projects Completion Rate</div>
        <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $projectCompletionRate }}%</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="text-xs text-slate-500">Teams Activity Rate</div>
        <div class="text-2xl font-bold text-amber-600 mt-1">{{ $teamActivityRate }}%</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Submit Detailed Report -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 h-fit">
        <h2 class="font-bold text-slate-800 mb-3">Submit a Detailed Report</h2>

        <form method="POST" action="{{ route('reports.store') }}" class="space-y-3">
            @csrf

            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Report Type</label>
                <select name="report_type" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    @foreach(['Project Progress', 'Meetings Summary', 'Worker Performance', 'Tasks', 'KPIs'] as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Project</label>
                <select name="project_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">All / General</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" class="w-full rounded-lg border border-slate-300 px-2 py-2 text-xs">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">End Date</label>
                    <input type="date" name="end_date" class="w-full rounded-lg border border-slate-300 px-2 py-2 text-xs">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Note / Summary</label>
                <textarea name="note" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
            </div>

            <button type="submit" class="w-full py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                Submit Report
            </button>
        </form>
    </div>

    <!-- Submitted Reports -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-5">
        <h2 class="font-bold text-slate-800 mb-3">Submitted Reports</h2>
        <div class="divide-y">
            @forelse($reports as $report)
                <div class="py-3 space-y-1">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-bold text-slate-900">{{ $report->report_type }}</span>
                        <span class="text-xs text-slate-400">{{ $report->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="text-xs text-slate-500">
                        Project: {{ $report->project?->name ?? 'General' }} · By {{ $report->submitter?->name }}
                    </div>
                    @if($report->note)
                        <p class="text-sm text-slate-700 pt-1">{{ $report->note }}</p>
                    @endif
                </div>
            @empty
                <p class="text-sm text-slate-400 py-6 text-center">No reports submitted yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection