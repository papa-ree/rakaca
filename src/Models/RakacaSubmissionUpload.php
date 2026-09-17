<?php

namespace Paparee\Rakaca\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RakacaSubmissionUpload extends Model
{
    use HasUuids;

    protected $table = 'rakaca_submission_uploads';

    protected $fillable = [
        'rakaca_submission_id',
        'user_uuid',
        'file_path',
        'original_name',
        'mime_type',
        'size',
    ];

    protected $casts = [
        'created_at' => 'datetime:d M Y H:i',
        'updated_at' => 'datetime:d M Y H:i',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(RakacaSubmission::class, 'rakaca_submission_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
