<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sensors = Sensor::with('zone')
            ->when($request->status,  fn ($q, $v) => $q->where('status', $v))
            ->when($request->zone_id, fn ($q, $v) => $q->where('zone_id', $v))
            ->orderBy('created_at')
            ->get();

        return response()->json($sensors);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'zone_id'       => 'nullable|exists:zones,id',
            'type'          => 'required|in:motion,thermal,camera,vibration,gas,smoke',
            'status'        => 'sometimes|in:online,offline,alert,maintenance',
            'battery_level' => 'sometimes|integer|min:0|max:100',
            'last_ping'     => 'nullable|date',
            'x_percent'     => 'nullable|numeric|min:0|max:100',
            'y_percent'     => 'nullable|numeric|min:0|max:100',
            'metadata'      => 'nullable|array',
        ]);

        $data['last_ping'] = $data['last_ping'] ?? now();
        $data['metadata']  = $data['metadata']  ?? [];

        $sensor = Sensor::create($data);

        return response()->json($sensor->load('zone'), 201);
    }

    public function show(Sensor $sensor): JsonResponse
    {
        return response()->json(
            $sensor->load(['zone', 'readings' => fn ($q) => $q->latest('recorded_at')->limit(50)])
        );
    }

    public function update(Request $request, Sensor $sensor): JsonResponse
    {
        $data = $request->validate([
            'name'          => 'sometimes|string|max:255',
            'zone_id'       => 'nullable|exists:zones,id',
            'type'          => 'sometimes|in:motion,thermal,camera,vibration,gas,smoke',
            'status'        => 'sometimes|in:online,offline,alert,maintenance',
            'battery_level' => 'sometimes|integer|min:0|max:100',
            'last_ping'     => 'nullable|date',
            'x_percent'     => 'nullable|numeric|min:0|max:100',
            'y_percent'     => 'nullable|numeric|min:0|max:100',
            'metadata'      => 'nullable|array',
        ]);

        $sensor->update($data);

        return response()->json($sensor->load('zone'));
    }

    public function destroy(Sensor $sensor): JsonResponse
    {
        $sensor->delete();

        return response()->json(null, 204);
    }
}
