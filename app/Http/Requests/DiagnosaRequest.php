<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiagnosaRequest extends FormRequest
{
    /**
     * Menentukan apakah pengguna diizinkan untuk membuat request ini.
     */
    public function authorize(): bool
    {
        return true; // Izinkan semua untuk saat ini
    }

    /**
     * Mendapatkan aturan validasi yang diterapkan pada request.
     */
    public function rules(): array
    {
        return [
            'ph_aktual'     => 'required|numeric|min:0|max:14',
            'ec_aktual'     => 'required|numeric|min:0|max:10',
            'ppm_aktual'    => 'required|integer|min:0|max:5000',
            'suhu_aktual'   => 'nullable|numeric|min:0|max:50',
            'sesi_tanam_id' => 'nullable|exists:sesi_tanam,id',
            'tanaman_id'    => 'nullable|exists:tanaman,id',
            'fase'          => 'nullable|string',
        ];
    }
}
