<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoUploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => 'required|file|image|max:10240', // 10 MB max
        ]);

        $file     = $request->file('photo');
        $filename = $file->getClientOriginalName();

        // Store in storage/app/public/ — served at /storage/FILENAME
        $path = $file->storeAs('', $filename, 'public');

        return response()->json([
            'filename' => $filename,
            'url'      => asset('storage/' . $filename),
        ], 201);
    }
}
