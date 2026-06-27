<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tanaman extends Model
{
    protected $table = 'tanaman';

    protected $fillable = [
        'nama',
        'emoji',
        'kategori',
        'foto_url',
    ];

    public function rules(): HasMany
    {
        return $this->hasMany(RuleNutrisi::class, 'tanaman_id');
    }

    public function sesiTanams(): HasMany
    {
        return $this->hasMany(SesiTanam::class, 'tanaman_id');
    }
}
