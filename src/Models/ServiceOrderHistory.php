<?php

namespace Paparee\Rakaca\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ServiceOrderHistory extends Model
{
    use HasUuids;

    protected $table = 'service_order_histories';

    protected $fillable = [
        'user_uuid',
        'rakaca_service_id',
        'status',
        'data',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function service()
    {
        return $this->belongsTo(RakacaService::class, 'rakaca_service_id', 'id');
    }
}
