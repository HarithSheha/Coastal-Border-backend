<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorReading extends Model
{
    use HasUuids;

    protected $fillable = [
        'sensor_id', 'value', 'unit', 'triggered', 'recorded_at',
    ];

    protected $casts = [
        'triggered'   => 'boolean',
        'recorded_at' => 'datetime',
        'value'       => 'float',
    ];

    public function sensor(): BelongsTo
    {
        return $this->belongsTo(Sensor::class);
    }
}
