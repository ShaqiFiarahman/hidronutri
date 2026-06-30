<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaseTanaman extends Model
{
    protected $table = 'fase_tanaman';

    protected $fillable = [
        'tanaman_id',
        'fase',
        'urutan',
        'durasi_hari',
        'kumulatif_hari',
    ];

    protected $casts = [
        'urutan'         => 'integer',
        'durasi_hari'    => 'integer',
        'kumulatif_hari' => 'integer',
    ];

    public function tanaman(): BelongsTo
    {
        return $this->belongsTo(Tanaman::class, 'tanaman_id');
    }
}
