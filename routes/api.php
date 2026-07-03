<?php

use App\Http\Controllers\Api\ZoneController;
use App\Http\Controllers\Api\SensorController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\LiveReportController;
use App\Http\Controllers\Api\UrgencyController;
use App\Http\Controllers\Api\SensorReadingController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json([
    'status'    => 'ok',
    'app'       => 'Coastal Border Security API',
    'version'   => '1.0',
    'endpoints' => [
        'zones'           => url('/api/zones'),
        'sensors'         => url('/api/sensors'),
        'reports'         => url('/api/reports'),
        'urgencies'       => url('/api/urgencies'),
        'live_reports'    => url('/api/live-reports'),
        'sensor_readings' => url('/api/sensor-readings'),
    ],
]));

Route::apiResource('zones', ZoneController::class);
Route::apiResource('sensors', SensorController::class);
Route::apiResource('reports', ReportController::class);
Route::apiResource('urgencies', UrgencyController::class);
Route::apiResource('live-reports', LiveReportController::class);
Route::apiResource('sensor-readings', SensorReadingController::class)->only(['index', 'store', 'show']);
