<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LiveReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LiveReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $liveReports = LiveReport::with(['zone', 'sensor'])
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

        return response()->json($liveReports);
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

        $liveReport = LiveReport::create($data);

        return response()->json($liveReport->load(['zone', 'sensor']), 201);
    }

    public function show(LiveReport $liveReport): JsonResponse
    {
        return response()->json($liveReport->load(['zone', 'sensor']));
    }

    public function update(Request $request, LiveReport $liveReport): JsonResponse
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

        $liveReport->update($data);

        return response()->json($liveReport->load(['zone', 'sensor']));
    }

    public function destroy(LiveReport $liveReport): JsonResponse
    {
        $liveReport->delete();

        return response()->json(null, 204);
    }
}
