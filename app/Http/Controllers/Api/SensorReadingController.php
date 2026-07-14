<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SensorReading;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SensorReadingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $readings = SensorReading::with(['sensor', 'sensor.zone'])
            ->when($request->sensor_id, fn ($q, $v) => $q->where('sensor_id', $v))
            ->when($request->triggered,  fn ($q)    => $q->where('triggered', true))
            ->orderByDesc('recorded_at')
            ->limit(200)
            ->get();

        return response()->json($readings);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'sensor_id'   => 'required|exists:sensors,id',
            'value'       => 'required|numeric',
            'unit'        => 'required|string|max:50',
            'triggered'   => 'sometimes|boolean',
            'recorded_at' => 'nullable|date',
        ]);

        $data['recorded_at'] = $data['recorded_at'] ?? now();

        $reading = SensorReading::create($data);

        return response()->json($reading, 201);
    }

    public function show(SensorReading $sensorReading): JsonResponse
    {
        return response()->json($sensorReading->load('sensor'));
    }
}
