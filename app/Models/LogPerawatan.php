<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogPerawatan extends Model
{
    protected $table = 'log_perawatan';

    protected $fillable = [
        'sesi_tanam_id',
        'tanggal',
        'tipe',
        'ph',
        'ppm',
        'catatan',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'ph' => 'decimal:2',
        'ppm' => 'integer',
    ];

    public function sesiTanam()
    {
        return $this->belongsTo(SesiTanam::class, 'sesi_tanam_id');
    }
}
