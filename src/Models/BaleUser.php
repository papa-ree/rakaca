<?php

namespace Paparee\Rakaca\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BaleUser extends Model
{
    use HasUuids;

    protected $table = 'bale_users';

    protected $fillable = [
        'bale_id',
        'user_uuid',
        'role',
    ];

    /**
     * Get the bale list that this user belongs to.
     */
    public function bale(): BelongsTo
    {
        return $this->belongsTo(BaleList::class, 'bale_id', 'id');
    }

    /**
     * Get the user for this bale assignment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
