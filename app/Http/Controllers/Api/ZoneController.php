<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function index(): JsonResponse
    {
        $zones = Zone::withCount(['sensors', 'reports'])
            ->orderBy('created_at')
            ->get();

        return response()->json($zones);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'type'           => 'required|in:restricted,danger,caution,checkpoint',
            'status'         => 'sometimes|in:active,inactive,breach',
            'color'          => 'nullable|string|max:20',
            'x_percent'      => 'nullable|numeric|min:0|max:100',
            'y_percent'      => 'nullable|numeric|min:0|max:100',
            'width_percent'  => 'nullable|numeric|min:0|max:100',
            'height_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        $zone = Zone::create($data);

        return response()->json($zone, 201);
    }

    public function show(Zone $zone): JsonResponse
    {
        return response()->json($zone->load(['sensors', 'reports']));
    }

    public function update(Request $request, Zone $zone): JsonResponse
    {
        $data = $request->validate([
            'name'           => 'sometimes|string|max:255',
            'description'    => 'nullable|string',
            'type'           => 'sometimes|in:restricted,danger,caution,checkpoint',
            'status'         => 'sometimes|in:active,inactive,breach',
            'color'          => 'nullable|string|max:20',
            'x_percent'      => 'nullable|numeric|min:0|max:100',
            'y_percent'      => 'nullable|numeric|min:0|max:100',
            'width_percent'  => 'nullable|numeric|min:0|max:100',
            'height_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        $zone->update($data);

        return response()->json($zone);
    }

    public function destroy(Zone $zone): JsonResponse
    {
        $zone->delete();

        return response()->json(null, 204);
    }
}
