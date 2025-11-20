<?php

namespace Paparee\Rakaca\Traits;

use Paparee\Rakaca\Models\PersonHasService;
use Paparee\Rakaca\Models\RakacaService;

trait HasServiceRelation
{
    /**
     * Relasi ke tabel person_has_services
     */
    public function personServices()
    {
        return $this->hasMany(PersonHasService::class, 'user_uuid', 'uuid');
    }

    /**
     * Relasi many-to-many ke services melalui person_has_services
     */
    public function services()
    {
        return $this->belongsToMany(
            RakacaService::class,
            'person_has_services',
            'user_uuid',
            'rakaca_service_id',
            'uuid',
            'id'
        )->withTimestamps();
    }

    /**
     * Cek apakah user memiliki service tertentu
     */
    public function hasService(string $slug): bool
    {
        static $cachedServices = [];

        // Cache sementara agar tidak query berulang
        if (!isset($cachedServices[$this->uuid])) {
            $cachedServices[$this->uuid] = $this->services->pluck('slug')->toArray();
        }

        return in_array($slug, $cachedServices[$this->uuid]);
    }
}
