<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $reports = Report::with(['zone', 'sensor'])
            ->when($request->status,   fn ($q, $v) => $q->where('status', $v))
            ->when($request->severity, fn ($q, $v) => $q->where('severity', $v))
            ->when($request->type,     fn ($q, $v) => $q->where('type', $v))
            ->when($request->zone_id,  fn ($q, $v) => $q->where('zone_id', $v))
            ->when($request->search,   fn ($q, $v) => $q->where(function ($q) use ($v) {
                $q->where('title', 'like', "%{$v}%")
                  ->orWhere('description', 'like', "%{$v}%")
                  ->orWhere('reporter_name', 'like', "%{$v}%");
            }))
            ->orderByDesc('created_at')
            ->get();

        return response()->json($reports);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'type'             => 'required|in:intrusion,vandalism,suspicious,environmental,sensor_alert,other',
            'severity'         => 'required|in:low,medium,high,critical',
            'status'           => 'sometimes|in:open,investigating,resolved,dismissed',
            'source'           => 'required|in:mobile,sensor,manual',
            'zone_id'          => 'nullable|exists:zones,id',
            'sensor_id'        => 'nullable|exists:sensors,id',
            'reporter_name'    => 'required|string|max:255',
            'reporter_contact' => 'nullable|string|max:255',
            'latitude'         => 'nullable|numeric|between:-90,90',
            'longitude'        => 'nullable|numeric|between:-180,180',
            'image_url'        => 'nullable|string|max:500',
        ]);

        $report = Report::create($data);

        return response()->json($report->load(['zone', 'sensor']), 201);
    }

    public function show(Report $report): JsonResponse
    {
        return response()->json($report->load(['zone', 'sensor']));
    }

    public function update(Request $request, Report $report): JsonResponse
    {
        $data = $request->validate([
            'title'            => 'sometimes|string|max:255',
            'description'      => 'nullable|string',
            'type'             => 'sometimes|in:intrusion,vandalism,suspicious,environmental,sensor_alert,other',
            'severity'         => 'sometimes|in:low,medium,high,critical',
            'status'           => 'sometimes|in:open,investigating,resolved,dismissed',
            'source'           => 'sometimes|in:mobile,sensor,manual',
            'zone_id'          => 'nullable|exists:zones,id',
            'sensor_id'        => 'nullable|exists:sensors,id',
            'reporter_name'    => 'sometimes|string|max:255',
            'reporter_contact' => 'nullable|string|max:255',
            'latitude'         => 'nullable|numeric|between:-90,90',
            'longitude'        => 'nullable|numeric|between:-180,180',
            'image_url'        => 'nullable|string|max:500',
        ]);

        $report->update($data);

        return response()->json($report->load(['zone', 'sensor']));
    }

    public function destroy(Report $report): JsonResponse
    {
        $report->delete();

        return response()->json(null, 204);
    }
}
