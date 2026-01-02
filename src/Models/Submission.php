<?php

namespace Paparee\Rakaca\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_uuid',
        'rakaca_service_id',
        'code',
        'status',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(RakacaService::class, 'rakaca_service_id');
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'amber',
            'approved' => 'green',
            'rejected' => 'red',
            'review' => 'blue',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'review' => 'Dalam Review',
            default => 'Unknown',
        };
    }
}
