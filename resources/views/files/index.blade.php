@extends('layouts.app')

@section('title', 'Files')
@section('header', 'Files Repository')

@section('content')
<!-- Upload File Form -->
<form method="POST" action="{{ route('files.store') }}" enctype="multipart/form-data" class="bg-white p-4 rounded-xl border border-slate-200 mb-6 flex flex-wrap items-center gap-3">
    @csrf
    <select name="project_id" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <option value="">Select Project</option>
        @foreach($projects as $project)
            <option value="{{ $project->id }}">{{ $project->name }}</option>
        @endforeach
    </select>
    <input type="file" name="file" required class="text-sm text-slate-600">
    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
        Upload File +
    </button>
</form>

<!-- Recent Files -->
<div class="mb-6">
    <h2 class="font-bold text-slate-800 mb-3">Recent Files</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @forelse($recentFiles as $file)
            <div class="bg-white rounded-xl border border-slate-200 p-4 flex flex-col justify-between">
                <div>
                    <div class="font-semibold text-sm text-slate-800 truncate">{{ $file->name }}</div>
                    <div class="text-xs text-slate-500 mt-1">{{ $file->project?->name }} · By {{ $file->uploader?->name }}</div>
                </div>
                <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-400">{{ $file->formatted_size }}</span>
                    <a href="{{ route('files.download', $file) }}" class="text-indigo-600 font-semibold hover:underline">Download</a>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-400">No recent files.</p>
        @endforelse
    </div>
</div>

<!-- All Files Filter & Table -->
<div class="bg-white rounded-xl border border-slate-200 p-5">
    <form method="GET" action="{{ route('files.index') }}" class="flex flex-wrap gap-2 mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search file name..."
               class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <select name="project_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All Projects</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}" @selected((string)request('project_id') === (string)$project->id)>
                    {{ $project->name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-medium">Filter</button>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-slate-500 border-b">
                <tr>
                    <th class="py-2">File Name</th>
                    <th class="py-2">Project</th>
                    <th class="py-2">Uploaded By</th>
                    <th class="py-2">Size</th>
                    <th class="py-2">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($allFiles as $file)
                    <tr>
                        <td class="py-2.5 font-medium">{{ $file->name }}</td>
                        <td class="py-2.5 text-slate-500">{{ $file->project?->name }}</td>
                        <td class="py-2.5 text-slate-500">{{ $file->uploader?->name }}</td>
                        <td class="py-2.5 text-slate-500">{{ $file->formatted_size }}</td>
                        <td class="py-2.5">
                            <a href="{{ route('files.download', $file) }}" class="text-indigo-600 hover:underline text-xs font-semibold">
                                Download
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-slate-400">No files found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection