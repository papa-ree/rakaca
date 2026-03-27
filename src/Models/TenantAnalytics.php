<?php

namespace Paparee\Rakaca\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TenantAnalytics extends Model
{
    use HasUuids;

    protected $fillable = [
        'bale_id',
        'provider',
        'website_id',
        'domain',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];
}
