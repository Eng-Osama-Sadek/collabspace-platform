@extends('layouts.app')

@section('title', $user->name)
@section('header', 'Team Member Profile > ' . $user->name)

@section('content')
<div class="bg-white rounded-xl border border-slate-200 p-5 mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <div class="text-xl font-bold text-slate-900">{{ $user->name }}</div>
        <div class="text-sm text-slate-500">{{ $user->job_title }} · Joined {{ $user->joined_at?->format('M Y') ?? $user->created_at->format('M Y') }}</div>
    </div>
    <div class="flex gap-6 text-center">
        <div>
            <div class="text-xs text-slate-500">Completed Tasks</div>
            <div class="text-xl font-bold text-emerald-600">{{ $doneTasks->count() }}</div>
        </div>
        <div>
            <div class="text-xs text-slate-500">Hours Worked</div>
            <div class="text-xl font-bold text-indigo-600">{{ $user->hours_worked }}</div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-4">
        <div>
            <h2 class="font-bold text-slate-800 mb-2">About</h2>
            <p class="text-sm text-slate-600">{{ $user->about ?? 'Team member in ' . $user->department . '.' }}</p>
        </div>
        <div>
            <h2 class="font-bold text-slate-800 mb-2">Recent Activities</h2>
            <ul class="space-y-2 text-xs text-slate-600">
                @forelse($user->activityLogs as $log)
                    <li class="py-1.5 border-b border-slate-100 flex justify-between">
                        <span>{{ $log->action_text }}</span>
                        <span class="text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                    </li>
                @empty
                    <li class="text-slate-400">No recent activities.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="font-bold text-slate-800 mb-3">To Do ({{ $todoTasks->count() }})</h2>
            <ul class="space-y-2 text-sm">
                @forelse($todoTasks as $task)
                    <li class="p-2.5 rounded-lg bg-slate-50">{{ $task->title }}</li>
                @empty
                    <li class="text-xs text-slate-400">No pending tasks.</li>
                @endforelse
            </ul>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="font-bold text-slate-800 mb-3">Done ({{ $doneTasks->count() }})</h2>
            <ul class="space-y-2 text-sm">
                @forelse($doneTasks as $task)
                    <li class="p-2.5 rounded-lg bg-emerald-50 text-emerald-900">{{ $task->title }}</li>
                @empty
                    <li class="text-xs text-slate-400">No completed tasks yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection