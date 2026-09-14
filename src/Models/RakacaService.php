<?php

namespace Paparee\Rakaca\Models;

use Bale\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RakacaService extends Model
{
    use HasUuids, LogsActivity;

    protected $table = 'rakaca_services';

    protected $fillable = [
        'id',
        'name',
        'slug',
        'icon',
        'description',
        'actived',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    protected $casts = [
        'actived' => 'boolean',
        'created_at' => 'datetime:d M Y',
        'updated_at' => 'datetime:d M Y',
    ];

    public function persons()
    {
        return $this->hasMany(PersonHasService::class, 'rakaca_service_id', 'id');
    }

    public function forms()
    {
        return $this->hasMany(Form::class, 'rakaca_service_id', 'id');
    }
}
