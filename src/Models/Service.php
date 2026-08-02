<?php

namespace Paparee\Rakaca\Models;

use Bale\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    use HasUuids;
    use LogsActivity;

    protected $table = 'rakaca_services';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'slug',
        'icon',
        'description',
        'actived',
    ];

    protected $casts = [
        'actived' => 'boolean',
        'created_at' => 'datetime:d M Y',
        'updated_at' => 'datetime:d M Y',
    ];
}
