<?php

namespace Paparee\Rakaca\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aduan extends Model
{
    use HasUuids;

    protected $table = 'rakaca_aduans';

    protected $fillable = [
        'ref_code',
        'nama_lengkap',
        'nip',
        'wa_number',
        'aduan_category_id',
        'deskripsi',
        'status',
        'ip_address',
    ];

    protected $casts = [
        'nama_lengkap' => 'encrypted',
        'nip' => 'encrypted',
        'wa_number' => 'encrypted',
        'created_at' => 'datetime:d M Y H:i',
        'updated_at' => 'datetime:d M Y H:i',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AduanCategory::class, 'aduan_category_id');
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'amber',
            'processed' => 'blue',
            'done' => 'green',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu',
            'processed' => 'Diproses',
            'done' => 'Selesai',
            default => 'Unknown',
        };
    }
}
