<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RekomendasiProsesRequest extends FormRequest
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
            'tanggal_mulai'     => 'required|date|before_or_equal:today',
            'sistem_hidroponik' => 'required|in:nft,dft,rakit_apung,wick',
        ];
    }
}
