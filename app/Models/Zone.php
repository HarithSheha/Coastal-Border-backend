<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    use HasUuids;

    protected $fillable = [
        'name', 'description', 'type', 'status', 'color',
        'x_percent', 'y_percent', 'width_percent', 'height_percent',
    ];

    public function sensors(): HasMany
    {
        return $this->hasMany(Sensor::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function liveReports(): HasMany
    {
        return $this->hasMany(LiveReport::class);
    }
}
