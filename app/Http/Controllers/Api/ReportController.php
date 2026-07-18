<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

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
            ->get()
            // Tell the frontend whether a photo is available without sending the raw data
            ->map(fn ($r) => array_merge($r->toArray(), ['has_photo' => !is_null($r->photo_data)]));

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
            'photo'            => 'nullable',
            'name'             => 'required|string|max:100',
            'phone'            => 'required|string|max:20',
            'urgency_id'       => 'required|exists:urgencies,urgency_id',
        ]);

        if ($request->hasFile('photo')) {
            // Web / direct multipart upload
            $file = $request->file('photo');
            $data['photo']      = $file->getClientOriginalName();
            $data['photo_data'] = base64_encode(file_get_contents($file->path()));
        } elseif (!empty($data['photo'])) {
            // Mobile app flow: photo was uploaded via /upload-photo first;
            // the file now lives in storage/app/public — capture binary before it can be lost
            $filename = $data['photo'];
            if (Storage::disk('public')->exists($filename)) {
                $data['photo_data'] = base64_encode(Storage::disk('public')->get($filename));
            }
        }

        $report = Report::create($data);

        $result = array_merge($report->load(['zone', 'urgency'])->toArray(), [
            'has_photo' => !is_null($report->photo_data),
        ]);

        return response()->json($result, 201);
    }

    public function show(Report $report): JsonResponse
    {
        $result = array_merge($report->load(['zone', 'urgency'])->toArray(), [
            'has_photo' => !is_null($report->photo_data),
        ]);
        return response()->json($result);
    }

    /**
     * Serve the stored photo binary directly from the database.
     */
    public function servePhoto(Report $report): Response
    {
        if (is_null($report->photo_data)) {
            abort(404);
        }

        $binary   = base64_decode($report->photo_data);
        $mimeType = 'image/jpeg';

        // Detect PNG from magic bytes
        if (str_starts_with($binary, "\x89PNG")) {
            $mimeType = 'image/png';
        }

        return response($binary, 200, [
            'Content-Type'  => $mimeType,
            'Cache-Control' => 'public, max-age=86400',
        ]);
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
            'photo'            => 'sometimes|nullable|string|max:255',
            'name'             => 'sometimes|string|max:100',
            'phone'            => 'sometimes|string|max:20',
            'urgency_id'       => 'sometimes|exists:urgencies,urgency_id',
            'status'           => 'sometimes|in:unresolved,resolved',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $data['photo']      = $file->getClientOriginalName();
            $data['photo_data'] = base64_encode(file_get_contents($file->path()));
        } elseif (!empty($data['photo']) && $data['photo'] !== $report->photo) {
            // Mobile app updated the photo filename — capture binary from storage
            $filename = $data['photo'];
            if (Storage::disk('public')->exists($filename)) {
                $data['photo_data'] = base64_encode(Storage::disk('public')->get($filename));
            }
        }

        $report->update($data);

        $result = array_merge($report->load(['zone', 'urgency'])->toArray(), [
            'has_photo' => !is_null($report->photo_data),
        ]);

        return response()->json($result);
    }

    public function destroy(Report $report): JsonResponse
    {
        $report->delete();

        return response()->json(null, 204);
    }
}
