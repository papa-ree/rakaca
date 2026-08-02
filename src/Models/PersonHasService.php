<?php

namespace Paparee\Rakaca\Models;

use App\Models\User;
use Bale\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PersonHasService extends Model
{
    use HasUuids, LogsActivity;

    protected $table = 'person_has_services';

    protected $fillable = [
        'user_uuid',
        'rakaca_service_id',
        'actived',
    ];

    protected $casts = [
        'created_at' => 'datetime:d M Y',
        'updated_at' => 'datetime:d M Y',
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
