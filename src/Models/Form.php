<?php

namespace Paparee\Rakaca\Models;

use Bale\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Form extends Model
{
    use HasFactory;
    use HasUuids;
    use LogsActivity;

    protected $table = 'rakaca_forms';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'rakaca_service_id',
        'name',
        'slug',
        'meta',
        'actived',
    ];

    protected $casts = [
        'meta' => 'array',
        'actived' => 'boolean',
        'created_at' => 'datetime:d M Y',
        'updated_at' => 'datetime:d M Y',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(RakacaService::class, 'rakaca_service_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'rakaca_form_id');
    }
}