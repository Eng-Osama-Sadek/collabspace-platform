<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Report::query()->with(['project', 'submitter']);

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->integer('project_id'));
        }

        if ($request->filled('report_type')) {
            $query->where('report_type', $request->string('report_type'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Reports retrieved successfully.',
            'data' => $query->latest()->paginate($request->integer('per_page', 15)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'report_type' => ['required', 'string', 'max:100'],
            'note' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        $report = Report::create([
            'project_id' => $validated['project_id'] ?? null,
            'submitted_by' => $request->user()?->id,
            'report_type' => $validated['report_type'],
            'note' => $validated['note'] ?? null,
            'start_date' => $validated['start_date'] ?? now()->startOfWeek()->toDateString(),
            'end_date' => $validated['end_date'] ?? now()->toDateString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Report generated and stored successfully.',
            'data' => $report->load(['project', 'submitter']),
        ], 201);
    }
}
