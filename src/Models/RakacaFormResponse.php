<?php

namespace Paparee\Rakaca\Models;

use App\Models\User;
use Bale\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RakacaFormResponse extends Model
{
    use HasUuids, LogsActivity, SoftDeletes;

    protected $table = 'rakaca_form_responses';

    protected $fillable = [
        'rakaca_submission_id',
        'processed_at',
        'processed_by',
        'rejection_reason',
        'rejected_at',
        'revise_note',
        'requested_revision_at',
        'resolution_data',
        'resolved_at',
        'resolved_by',
        'cancelled_reason',
    ];

    protected $casts = [
        'resolution_data' => 'array',
        'processed_at' => 'datetime',
        'rejected_at' => 'datetime',
        'requested_revision_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(RakacaSubmission::class, 'rakaca_submission_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by', 'uuid');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by', 'uuid');
    }
}
