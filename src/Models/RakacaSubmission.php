<?php

namespace Paparee\Rakaca\Models;

use Bale\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RakacaSubmission extends Model
{
    use HasUuids, LogsActivity;

    protected $table = 'rakaca_submissions';

    protected $fillable = [
        'user_uuid',
        'rakaca_form_id',
        'code',
        'status',
        'items',
        'admin_response',
        'processed_at',
        'processed_by',
    ];

    protected $casts = [
        'items' => 'array',
        'processed_at' => 'datetime',
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

    public function uploads(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RakacaSubmissionUpload::class, 'rakaca_submission_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'processed_by', 'uuid');
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'amber',
            'approved' => 'green',
            'rejected' => 'red',
            'review' => 'blue',
            'ditutup' => 'slate',
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
            'ditutup' => 'Ditutup',
            default => 'Unknown',
        };
    }
}
