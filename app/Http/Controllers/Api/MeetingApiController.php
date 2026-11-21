<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeetingApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Meeting::query()->with('project');

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->integer('project_id'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Meetings retrieved successfully.',
            'data' => $query->latest('start_at')->paginate($request->integer('per_page', 15)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'agenda' => ['nullable', 'string'],
            'meeting_link' => ['nullable', 'string', 'max:500'],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date'],
            'transcript' => ['nullable', 'string'],
        ]);

        $meeting = Meeting::create([
            'project_id' => $validated['project_id'] ?? null,
            'title' => $validated['title'],
            'agenda' => $validated['agenda'] ?? null,
            'meeting_link' => $validated['meeting_link'] ?? null,
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'] ?? null,
            'transcript' => $validated['transcript'] ?? null,
            'created_by' => $request->user()?->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Meeting scheduled successfully.',
            'data' => $meeting->load('project'),
        ], 201);
    }

    public function show(Meeting $meeting): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Meeting details loaded.',
            'data' => $meeting->load('project'),
        ]);
    }

    public function update(Request $request, Meeting $meeting): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'agenda' => ['nullable', 'string'],
            'meeting_link' => ['nullable', 'string', 'max:500'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date'],
            'transcript' => ['nullable', 'string'],
            'ai_summary' => ['nullable', 'string'],
            'key_points' => ['nullable', 'string'],
        ]);

        $meeting->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Meeting updated successfully.',
            'data' => $meeting->fresh(),
        ]);
    }

    public function generateAiSummary(Request $request, Meeting $meeting): JsonResponse
    {
        $sourceText = $request->input('transcript') ?: $meeting->transcript ?: $meeting->agenda ?: $meeting->title;
        $lines = array_values(array_filter(array_map('trim', preg_split('/[\r\n\.]+/', (string) $sourceText))));
        $keyPoints = array_slice($lines, 0, 4);

        $summary = 'Executive Summary for "' . $meeting->title . '": ' . implode('. ', $keyPoints) . '.';
        $formattedPoints = implode("\n", array_map(fn ($item) => '- ' . $item, $keyPoints));

        $meeting->update([
            'transcript' => $request->input('transcript') ?: $meeting->transcript,
            'ai_summary' => $summary,
            'key_points' => $formattedPoints,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'AI meeting summary generated.',
            'data' => $meeting->fresh(),
        ]);
    }

    public function destroy(Meeting $meeting): JsonResponse
    {
        $meeting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Meeting deleted successfully.',
        ]);
    }
}
