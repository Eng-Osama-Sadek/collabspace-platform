@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard Overview')

@section('content')
<!-- Filter Bar -->
<form method="GET" action="{{ route('dashboard') }}" class="bg-white p-4 rounded-xl border border-slate-200 mb-6 flex flex-wrap items-center gap-3">
    <select name="project_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <option value="">All Projects</option>
        @foreach($projects as $project)
            <option value="{{ $project->id }}" @selected((string)$projectId === (string)$project->id)>
                {{ $project->name }}
            </option>
        @endforeach
    </select>

    <select name="period" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <option value="today" @selected($period === 'today')>Today</option>
        <option value="this_week" @selected($period === 'this_week')>This Week</option>
        <option value="this_month" @selected($period === 'this_month')>This Month</option>
        <option value="last_three_month" @selected($period === 'last_three_month')>Last Three Month</option>
        <option value="this_year" @selected($period === 'this_year')>This Year</option>
    </select>

    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium">
        Apply Filter
    </button>
</form>

<!-- Top KPI Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-5 rounded-xl border border-slate-200">
        <div class="text-xs text-slate-500">Pending Tasks</div>
        <div class="text-2xl font-bold text-amber-600 mt-1">{{ $pendingCount }}</div>
    </div>
    <div class="bg-white p-5 rounded-xl border border-slate-200">
        <div class="text-xs text-slate-500">In Progress</div>
        <div class="text-2xl font-bold text-indigo-600 mt-1">{{ $inProgressCount }}</div>
    </div>
    <div class="bg-white p-5 rounded-xl border border-slate-200">
        <div class="text-xs text-slate-500">Completed Tasks</div>
        <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $completedCount }}</div>
    </div>
    <div class="bg-white p-5 rounded-xl border border-slate-200">
        <div class="text-xs text-slate-500">Completion Rate</div>
        <div class="text-2xl font-bold text-slate-900 mt-1">{{ $completionRate }}%</div>
    </div>
</div>

<!-- Middle Grid: Active Projects & Upcoming Tasks -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-slate-800">Active Projects</h2>
            <a href="{{ route('projects.index') }}" class="text-xs text-indigo-600 hover:underline">View All</a>
        </div>
        @forelse($projects as $project)
            <div class="p-3 rounded-lg bg-slate-50 border border-slate-100">
                <div class="flex items-center justify-between text-sm font-semibold">
                    <a href="{{ route('projects.show', $project) }}" class="hover:text-indigo-600">{{ $project->name }}</a>
                    <span class="text-xs text-slate-600">{{ $project->completion_percentage }}%</span>
                </div>
                <div class="w-full bg-slate-200 h-2 rounded-full mt-2 overflow-hidden">
                    <div class="bg-indigo-600 h-full" style="width: {{ $project->completion_percentage }}%"></div>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-400">No projects yet.</p>
        @endforelse
    </div>

    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
            <h2 class="font-bold text-slate-800">Upcoming & Priority Tasks</h2>
            <div class="flex items-center gap-2 text-xs">
                <a href="{{ route('dashboard', ['priority' => 'high']) }}" class="px-2.5 py-1 rounded bg-rose-100 text-rose-700 font-medium">High</a>
                <a href="{{ route('dashboard', ['priority' => 'medium']) }}" class="px-2.5 py-1 rounded bg-amber-100 text-amber-700 font-medium">Medium</a>
                <a href="{{ route('dashboard', ['priority' => 'low']) }}" class="px-2.5 py-1 rounded bg-sky-100 text-sky-700 font-medium">Low</a>
                <a href="{{ route('tasks.index') }}" class="text-indigo-600 hover:underline ml-2">Board</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 border-b">
                    <tr>
                        <th class="py-2">Task</th>
                        <th class="py-2">Project</th>
                        <th class="py-2">Priority</th>
                        <th class="py-2">Progress</th>
                        <th class="py-2">Due</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($upcomingTasks as $task)
                        <tr>
                            <td class="py-2.5 font-medium">{{ $task->title }}</td>
                            <td class="py-2.5 text-slate-500">{{ $task->project?->name }}</td>
                            <td class="py-2.5 capitalize">{{ $task->priority }}</td>
                            <td class="py-2.5">{{ $task->progress }}%</td>
                            <td class="py-2.5 text-slate-500">{{ $task->due_date?->format('M d') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-slate-400">No open tasks.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bottom Grid: Teams Status, Upcoming Meetings, Recent Files & Ask AI -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-3">
        <h2 class="font-bold text-slate-800">Teams Status</h2>
        @foreach($teamsStatus as $member)
            <div class="flex items-center justify-between text-sm py-1.5 border-b border-slate-100 last:border-0">
                <div>
                    <div class="font-medium">{{ $member->name }}</div>
                    <div class="text-xs text-slate-400">{{ $member->department }} · {{ ucfirst($member->availability_status) }}</div>
                </div>
                <span class="text-xs font-semibold text-slate-700">
                    {{ $member->completed_tasks_count }}/{{ $member->tasks_count }} Tasks
                </span>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-4">
        <div>
            <h2 class="font-bold text-slate-800 mb-2">Upcoming Meetings</h2>
            @forelse($upcomingMeetings as $meeting)
                <div class="p-2.5 rounded-lg bg-slate-50 mb-2 text-sm">
                    <div class="font-semibold">{{ $meeting->title }}</div>
                    <div class="text-xs text-slate-500">{{ $meeting->start_at->format('M d, H:i') }} · {{ $meeting->project?->name ?? 'General' }}</div>
                </div>
            @empty
                <p class="text-xs text-slate-400">No upcoming meetings.</p>
            @endforelse
        </div>

        <div>
            <h2 class="font-bold text-slate-800 mb-2">Recent Files</h2>
            @forelse($recentFiles as $file)
                <div class="flex items-center justify-between text-xs py-1.5 border-b border-slate-100 last:border-0">
                    <span class="truncate font-medium">{{ $file->name }}</span>
                    <span class="text-slate-400">{{ $file->formatted_size }}</span>
                </div>
            @empty
                <p class="text-xs text-slate-400">No files uploaded yet.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5 flex flex-col justify-between">
        <div>
            <h2 class="font-bold text-slate-800 mb-2">Ask AI</h2>
            <p class="text-xs text-slate-500 mb-3">Quick status summary across your workspace projects and tasks.</p>

            @if(session('ai_reply'))
                <div class="p-3 rounded-lg bg-indigo-50 text-indigo-900 text-xs mb-3 leading-relaxed">
                    {{ session('ai_reply') }}
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('dashboard.ask-ai') }}" class="space-y-2">
            @csrf
            <input type="text" name="prompt" placeholder="Ask about project progress or open tasks..." required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <button type="submit" class="w-full py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-sm font-medium">
                Ask AI
            </button>
        </form>
    </div>
</div>
@endsection