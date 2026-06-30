<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sensor extends Model
{
    use HasUuids;

    protected $fillable = [
        'name', 'zone_id', 'type', 'status', 'battery_level',
        'last_ping', 'x_percent', 'y_percent', 'metadata',
    ];

    protected $casts = [
        'metadata'      => 'array',
        'last_ping'     => 'datetime',
        'battery_level' => 'integer',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function readings(): HasMany
    {
        return $this->hasMany(SensorReading::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
