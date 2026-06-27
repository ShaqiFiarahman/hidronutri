<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SesiTanam extends Model
{
    protected $table = 'sesi_tanam';

    protected $fillable = [
        'tanaman_id',
        'sistem_hidroponik',
        'fase_saat_ini',
        'tanggal_mulai',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
    ];

    public function tanaman(): BelongsTo
    {
        return $this->belongsTo(Tanaman::class, 'tanaman_id');
    }

    public function riwayatDiagnosas(): HasMany
    {
        return $this->hasMany(RiwayatDiagnosa::class, 'sesi_tanam_id');
    }
}
