<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $primaryKey = 'report_id';

    protected $fillable = [
        'date', 'latitude', 'longitude', 'address', 'zone_id',
        'color', 'number_of_people', 'description', 'photo', 'photo_data',
        'name', 'phone', 'urgency_id', 'status',
    ];

    // Never send the raw binary through the JSON list — only via the dedicated serve endpoint
    protected $hidden = ['photo_data'];

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
        // 'urgency_id' on both sides — must be explicit since the related PK is non-standard
        return $this->belongsTo(Urgency::class, 'urgency_id', 'urgency_id');
    }
}
