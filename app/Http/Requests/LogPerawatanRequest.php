<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogPerawatanRequest extends FormRequest
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
            'sesi_tanam_id' => 'required|exists:sesi_tanam,id',
            'tanggal'       => 'required|date',
            'tipe'          => 'required|in:cek,isi_ulang',
            'ph'            => 'nullable|numeric',
            'ppm'           => 'nullable|integer',
            'suhu'          => 'nullable|numeric',
            'catatan'       => 'nullable|string',
        ];
    }
}
