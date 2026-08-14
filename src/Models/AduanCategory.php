<?php

namespace Paparee\Rakaca\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AduanCategory extends Model
{
    use HasUuids;

    protected $table = 'rakaca_aduan_categories';

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'created_at' => 'datetime:d M Y H:i',
        'updated_at' => 'datetime:d M Y H:i',
    ];

    public function aduans(): HasMany
    {
        return $this->hasMany(Aduan::class, 'aduan_category_id');
    }
}
