<?php

namespace App\Http\Controllers;

use App\Models\SesiTanam;
use App\Models\RuleNutrisi;
use App\Models\Tanaman;
use Carbon\Carbon;
use App\Http\Requests\SesiTanamRequest;
use Illuminate\Http\Request;

class SesiTanamController extends Controller
{
    // Mapping durasi fase sekarang ada di App\Services\RuleBasedEngine

    /**
     * Memulai sesi tanam baru
     */
    public function store(SesiTanamRequest $request)
    {
        $validated = $request->validated();

        // Validasi bahwa fase valid untuk tanaman ini
        $ruleExists = RuleNutrisi::where('tanaman_id', $validated['tanaman_id'])
            ->where('fase', $validated['fase_saat_ini'])
            ->exists();

        if (!$ruleExists) {
            return back()->withErrors(['fase_saat_ini' => 'Fase yang dipilih tidak tersedia untuk tanaman ini.'])->withInput();
        }

        // Cek jika ada sesi tanam aktif sebelumnya, selesaikan secara otomatis (atau boleh ada banyak, tapi untuk kemudahan monitoring, kita selesaikan sesi aktif sebelumnya)
        // SesiTanam::where('status', 'aktif')->update(['status' => 'panen']);

        $sesi = SesiTanam::create([
            'tanaman_id' => $validated['tanaman_id'],
            'sistem_hidroponik' => $validated['sistem_hidroponik'],
            'fase_saat_ini' => $validated['fase_saat_ini'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'status' => 'aktif',
        ]);

        // Simpan juga ke session rekomendasi untuk sinkronisasi halaman cek-kondisi
        session([
            'rekomendasi_tanaman_id' => $sesi->tanaman_id,
            'rekomendasi_fase' => $sesi->fase_saat_ini,
            'rekomendasi_sistem' => $sesi->sistem_hidroponik,
        ]);

        return redirect('/hasil#jadwal')->with('success', 'Sesi tanam baru berhasil dimulai!');
    }

    /**
     * Menyelesaikan sesi tanam (panen)
     */
    public function panen($id)
    {
        $sesi = SesiTanam::findOrFail($id);
        $sesi->update([
            'status' => 'panen',
            'tanggal_panen' => now(),
        ]);

        session()->forget('aktif_sesi_id');

        return redirect('/riwayat')->with('success', 'Sesi tanam berhasil diselesaikan dan dicatat dalam riwayat panen!');
    }

    /**
     * Redirect ke halaman hasil tab jadwal
     */
    public function jadwal()
    {
        return redirect('/hasil#jadwal');
    }
}
