<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RuleNutrisi extends Model
{
    protected $table = 'rule_nutrisi';

    protected $fillable = [
        'tanaman_id',
        'fase',
        'ph_min',
        'ph_max',
        'ph_optimal_min',
        'ph_optimal_max',
        'ppm_min',
        'ppm_max',
        'dosis_a',
        'dosis_b',
        'ganti_larutan',
        'isi_ulang',
        'cek_ph_ec',
        'suhu_min',
        'suhu_max',
    ];

    protected $casts = [
        'ph_min' => 'decimal:2',
        'ph_max' => 'decimal:2',
        'ph_optimal_min' => 'decimal:2',
        'ph_optimal_max' => 'decimal:2',
        'dosis_a' => 'decimal:2',
        'dosis_b' => 'decimal:2',
        'ppm_min' => 'integer',
        'ppm_max' => 'integer',
        'ganti_larutan' => 'integer',
        'isi_ulang' => 'integer',
        'cek_ph_ec' => 'integer',
        'suhu_min' => 'integer',
        'suhu_max' => 'integer',
    ];

    public function tanaman(): BelongsTo
    {
        return $this->belongsTo(Tanaman::class, 'tanaman_id');
    }
}
