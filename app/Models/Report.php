<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $primaryKey = 'report_id';

    protected $fillable = [
        'date', 'latitude', 'longitude', 'address', 'zone_id',
        'color', 'number_of_people', 'description', 'photo',
        'name', 'phone', 'urgency_id',
    ];

    protected $casts = [
        'date'             => 'date',
        'latitude'         => 'decimal:8',
        'longitude'        => 'decimal:8',
        'number_of_people' => 'integer',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function urgency(): BelongsTo
    {
        return $this->belongsTo(Urgency::class);
    }
}
