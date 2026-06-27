<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatDiagnosa extends Model
{
    protected $table = 'riwayat_diagnosa';

    // Disable updated_at because it only has created_at
    const UPDATED_AT = null;

    protected $fillable = [
        'sesi_tanam_id',
        'ph_aktual',
        'ec_aktual',
        'ppm_aktual',
        'hasil_diagnosa',
    ];

    protected $casts = [
        'ph_aktual' => 'decimal:2',
        'ec_aktual' => 'decimal:2',
        'ppm_aktual' => 'integer',
        'hasil_diagnosa' => 'array',
    ];

    public function sesiTanam(): BelongsTo
    {
        return $this->belongsTo(SesiTanam::class, 'sesi_tanam_id');
    }
}
