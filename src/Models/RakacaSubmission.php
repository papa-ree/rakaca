<?php

namespace Paparee\Rakaca\Models;

use App\Models\User;
use Bale\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Paparee\Rakaca\Casts\SubmissionStatusCast;
use Paparee\Rakaca\Enums\SubmissionStatus;

class RakacaSubmission extends Model
{
    use HasUuids, LogsActivity, SoftDeletes;

    protected $table = 'rakaca_submissions';

    protected $fillable = [
        'user_uuid',
        'rakaca_form_id',
        'code',
        'status',
        'items',
        'status_changed_at',
        'files_finalized_at',
        'sla_target_days',
        'sla_started_at',
        'sla_due_at',
        'sla_paused_at',
    ];

    protected $casts = [
        'items' => 'array',
        'status' => SubmissionStatusCast::class,
        'status_changed_at' => 'datetime',
        'files_finalized_at' => 'datetime',
        'sla_target_days' => 'integer',
        'sla_started_at' => 'datetime',
        'sla_due_at' => 'datetime',
        'sla_paused_at' => 'datetime',
        'created_at' => 'datetime:d M Y',
        'updated_at' => 'datetime:d M Y',
    ];

    protected static function booted(): void
    {
        static::updating(function (RakacaSubmission $model) {
            if ($model->isDirty('status') && ! $model->isDirty('status_changed_at')) {
                $model->status_changed_at = now();
            }
        });

        static::deleting(function (RakacaSubmission $model) {
            if ($model->isForceDeleting()) {
                return;
            }

            $status = $model->status ?? SubmissionStatus::fromLegacy((string) $model->getRawOriginal('status'));

            if (! in_array($status, [SubmissionStatus::MenungguBerkas, SubmissionStatus::Ditolak], true)) {
                abort(403, 'Tiket operasional tidak boleh dihapus.');
            }
        });
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'rakaca_form_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function uploads(): HasMany
    {
        return $this->hasMany(RakacaSubmissionUpload::class, 'rakaca_submission_id');
    }

    public function response(): HasOne
    {
        return $this->hasOne(RakacaFormResponse::class, 'rakaca_submission_id');
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status?->color() ?? 'gray';
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status?->label() ?? 'Unknown';
    }

    /**
     * Status SLA untuk badge petugas. Null bila pengajuan tanpa SLA atau
     * sudah berakhir tanpa penilaian (ditolak/dibatalkan).
     */
    public function slaState(): ?string
    {
        if ($this->sla_due_at === null) {
            return null;
        }

        if ($this->sla_paused_at !== null) {
            return 'jeda';
        }

        if ($this->status === SubmissionStatus::Selesai) {
            $resolved = $this->response?->resolved_at;

            return $resolved !== null && $resolved->gt($this->sla_due_at) ? 'terlambat' : 'tepat-waktu';
        }

        if (in_array($this->status, [SubmissionStatus::Ditolak, SubmissionStatus::Dibatalkan], true)) {
            return null;
        }

        return now()->gt($this->sla_due_at) ? 'terlambat' : 'berjalan';
    }

    public function slaLabel(): ?string
    {
        return match ($this->slaState()) {
            'berjalan' => __('Berjalan'),
            'jeda' => __('Jeda'),
            'tepat-waktu' => __('Tepat waktu'),
            'terlambat' => __('Terlambat'),
            default => null,
        };
    }
}
