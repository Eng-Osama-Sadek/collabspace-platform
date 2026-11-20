<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    public function index(Request $request): View
    {
        $query = ProjectFile::with(['project', 'uploader'])->latest();

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->get('project_id'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $allFiles = $query->get();
        $recentFiles = ProjectFile::with(['project', 'uploader'])->latest()->take(4)->get();
        $projects = Project::orderBy('name')->get();

        return view('files.index', compact('allFiles', 'recentFiles', 'projects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $uploaded = $request->file('file');
        $path = $uploaded->store("projects/{$validated['project_id']}", 'public');

        ProjectFile::create([
            'project_id' => $validated['project_id'],
            'uploaded_by' => $request->user()->id,
            'name' => $uploaded->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $uploaded->getClientMimeType(),
            'size_bytes' => $uploaded->getSize(),
        ]);

        return back()->with('success', 'File uploaded successfully.');
    }

    public function download(ProjectFile $file): StreamedResponse|RedirectResponse
    {
        if (! Storage::disk('public')->exists($file->file_path)) {
            return back()->withErrors(['file' => 'Stored file not found on disk.']);
        }

        return Storage::disk('public')->download($file->file_path, $file->name);
    }
}