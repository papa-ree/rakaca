<?php

namespace Paparee\Rakaca\Models;

use Bale\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RakacaService extends Model
{
    use HasUuids, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime:d M Y',
        'updated_at' => 'datetime:d M Y',
    ];

    public function persons()
    {
        return $this->hasMany(PersonHasService::class, 'rakaca_service_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(ServiceOrderHistory::class, 'rakaca_service_id', 'id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'rakaca_service_id', 'id');
    }

    public function forms()
    {
        return $this->hasMany(Form::class, 'rakaca_service_id', 'id');
    }
}
