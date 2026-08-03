<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PhotoUploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => 'required|file|image|max:10240',
        ]);

        $file     = $request->file('photo');
        $filename = $file->getClientOriginalName();

        // Save to filesystem (keeps existing mobile app behaviour)
        $file->storeAs('', $filename, 'public');

        // Capture binary immediately into staging so it survives even if the
        // filesystem is wiped before the mobile creates the report record
        DB::table('photo_staging')->upsert(
            [
                'filename'   => $filename,
                'photo_data' => base64_encode(file_get_contents($file->path())),
                'created_at' => now(),
            ],
            ['filename'],
            ['photo_data', 'created_at']
        );

        return response()->json([
            'filename' => $filename,
            'url'      => asset('storage/' . $filename),
        ], 201);
    }
}
