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
        $reports = Report::with(['zone', 'urgency'])
            ->when($request->zone_id,    fn ($q, $v) => $q->where('zone_id', $v))
            ->when($request->urgency_id, fn ($q, $v) => $q->where('urgency_id', $v))
            ->when($request->date,       fn ($q, $v) => $q->whereDate('date', $v))
            ->when($request->search,     fn ($q, $v) => $q->where(function ($q) use ($v) {
                $q->where('address', 'like', "%{$v}%")
                  ->orWhere('description', 'like', "%{$v}%")
                  ->orWhere('name', 'like', "%{$v}%");
            }))
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($reports);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'date'             => 'required|date',
            'latitude'         => 'required|numeric|between:-90,90',
            'longitude'        => 'required|numeric|between:-180,180',
            'address'          => 'required|string|max:255',
            'zone_id'          => 'required|exists:zones,id',
            'color'            => 'required|string|max:50',
            'number_of_people' => 'required|integer|min:0',
            'description'      => 'nullable|string',
            'photo'            => 'nullable|string|max:255',
            'name'             => 'required|string|max:100',
            'phone'            => 'required|string|max:20',
            'urgency_id'       => 'required|exists:urgencies,urgency_id',
        ]);

        $report = Report::create($data);

        return response()->json($report->load(['zone', 'urgency']), 201);
    }

    public function show(Report $report): JsonResponse
    {
        return response()->json($report->load(['zone', 'urgency']));
    }

    public function update(Request $request, Report $report): JsonResponse
    {
        $data = $request->validate([
            'date'             => 'sometimes|date',
            'latitude'         => 'sometimes|numeric|between:-90,90',
            'longitude'        => 'sometimes|numeric|between:-180,180',
            'address'          => 'sometimes|string|max:255',
            'zone_id'          => 'sometimes|exists:zones,id',
            'color'            => 'sometimes|string|max:50',
            'number_of_people' => 'sometimes|integer|min:0',
            'description'      => 'nullable|string',
            'photo'            => 'nullable|string|max:255',
            'name'             => 'sometimes|string|max:100',
            'phone'            => 'sometimes|string|max:20',
            'urgency_id'       => 'sometimes|exists:urgencies,urgency_id',
        ]);

        $report->update($data);

        return response()->json($report->load(['zone', 'urgency']));
    }

    public function destroy(Report $report): JsonResponse
    {
        $report->delete();

        return response()->json(null, 204);
    }
}
