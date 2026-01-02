<?php

namespace Paparee\Rakaca\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RakacaService extends Model
{
    use HasUuids;

    protected $guarded = ['id'];

    public function persons()
    {
        return $this->hasMany(PersonHasService::class, 'rakaca_service_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(ServiceOrderHistory::class, 'rakaca_service_id', 'id');
    }
}