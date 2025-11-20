<?php

namespace Paparee\Rakaca\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PersonHasService extends Model
{
    use HasUuids;

    protected $table = 'person_has_services';

    protected $fillable = [
        'user_uuid',
        'rakaca_service_id',
        'actived',
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
