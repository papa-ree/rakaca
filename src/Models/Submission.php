<?php

namespace Paparee\Rakaca\Models;

use Bale\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    use HasUuids, LogsActivity;

    protected $table = 'rakaca_submissions';

    protected $fillable = [
        'user_uuid',
        'rakaca_form_id',
        'code',
        'status',
        'items',
    ];

    protected $casts = [
        'items' => 'array',
        'created_at' => 'datetime:d M Y',
        'updated_at' => 'datetime:d M Y',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'rakaca_form_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_uuid', 'uuid');
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