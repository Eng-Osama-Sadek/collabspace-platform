<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProjectFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FileApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ProjectFile::query()->with(['project', 'uploader']);

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->integer('project_id'));
        }

        if ($request->filled('task_id')) {
            $query->where('task_id', $request->integer('task_id'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Files retrieved successfully.',
            'data' => $query->latest()->paginate($request->integer('per_page', 20)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'task_id' => ['nullable', 'exists:tasks,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'file' => ['nullable', 'file', 'max:20480'],
            'file_path' => ['nullable', 'string', 'max:500'],
            'mime_type' => ['nullable', 'string', 'max:100'],
            'size_bytes' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('file')) {
            $uploaded = $request->file('file');
            $path = $uploaded->store('project-files', 'public');
            $name = $validated['name'] ?? $uploaded->getClientOriginalName();
            $mime = $uploaded->getClientMimeType();
            $size = $uploaded->getSize();
        } else {
            $name = $validated['name'] ?? 'document-' . now()->format('YmdHis') . '.pdf';
            $path = $validated['file_path'] ?? 'uploads/' . $name;
            $mime = $validated['mime_type'] ?? 'application/pdf';
            $size = $validated['size_bytes'] ?? 1024;
        }

        $record = ProjectFile::create([
            'project_id' => $validated['project_id'],
            'task_id' => $validated['task_id'] ?? null,
            'uploaded_by' => $request->user()?->id,
            'name' => $name,
            'file_path' => $path,
            'mime_type' => $mime,
            'size_bytes' => $size,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'File registered successfully.',
            'data' => $record->load(['project', 'uploader']),
        ], 201);
    }

    public function show(ProjectFile $file): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'File details loaded.',
            'data' => $file->load(['project', 'uploader']),
        ]);
    }

    public function destroy(ProjectFile $file): JsonResponse
    {
        $file->delete();

        return response()->json([
            'success' => true,
            'message' => 'File deleted successfully.',
        ]);
    }
}
