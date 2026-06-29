<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SesiTanamRequest extends FormRequest
{
    /**
     * Menentukan apakah pengguna diizinkan untuk membuat request ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Mendapatkan aturan validasi yang diterapkan pada request.
     */
    public function rules(): array
    {
        return [
            'tanaman_id'        => 'required|exists:tanaman,id',
            'sistem_hidroponik' => 'required|in:nft,dft,rakit_apung,wick',
            'fase_saat_ini'     => 'required|string|max:50',
            'tanggal_mulai'     => 'required|date|before_or_equal:today',
        ];
    }
}
