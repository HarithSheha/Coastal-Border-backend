<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Urgency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UrgencyController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Urgency::orderBy('urgency_id')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'urgency_level' => 'required|string|max:50',
        ]);

        $urgency = Urgency::create($data);

        return response()->json($urgency, 201);
    }

    public function show(Urgency $urgency): JsonResponse
    {
        return response()->json($urgency);
    }

    public function update(Request $request, Urgency $urgency): JsonResponse
    {
        $data = $request->validate([
            'urgency_level' => 'sometimes|string|max:50',
        ]);

        $urgency->update($data);

        return response()->json($urgency);
    }

    public function destroy(Urgency $urgency): JsonResponse
    {
        $urgency->delete();

        return response()->json(null, 204);
    }
}
