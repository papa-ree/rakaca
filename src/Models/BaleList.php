<?php

namespace Paparee\Rakaca\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BaleList extends Model
{
    use HasUuids;

    protected $table = 'bale_lists';

    protected $fillable = [
        'organization_id',
        'name',
        'slug',
        'database_host',
        'database_name',
        'database_username',
        'database_password',
        'storage_prefix',
        'is_active',
    ];

    /**
     * Atribut yang harus di-cast.
     * 'encrypted' akan otomatis encrypt saat simpan dan decrypt saat baca.
     */
    protected $casts = [
        'database_password' => 'encrypted',
        'database_username' => 'encrypted',
        'is_active' => 'boolean',
    ];

    /**
     * Get the organization that owns the bale list.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the bale users for this bale list.
     */
    public function baleUsers(): HasMany
    {
        return $this->hasMany(BaleUser::class, 'bale_id', 'id');
    }
}
