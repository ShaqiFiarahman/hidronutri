<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TindakanKorektif extends Model
{
    use HasFactory;
    
    protected $fillable = ['parameter', 'kondisi', 'tindakan'];
}
