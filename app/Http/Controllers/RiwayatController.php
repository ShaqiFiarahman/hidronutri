<?php

namespace App\Http\Controllers;

use App\Models\SesiTanam;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RiwayatController extends Controller
{
    /**
     * Menampilkan daftar riwayat sesi tanam (aktif & panen)
     */
    public function index()
    {
        $sesiAktif = SesiTanam::where('status', 'aktif')
            ->with('tanaman')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($sesi) {
                $tanggalMulai = Carbon::parse($sesi->tanggal_mulai);
                $usiaHari = max(0, $tanggalMulai->diffInDays(Carbon::today()));
                
                // lampirkan perhitungan usia ke objek untuk ditampilkan di tampilan
                $sesi->usia_hari = $usiaHari;
                
                // tetapkan perkiraan waktu total dan kemajuan pertumbuhan
                $durasiTotal = 35;
                $sesi->progress_persen = min(100, ($usiaHari / $durasiTotal) * 100);
                
                return $sesi;
            });

        $sesiPanen = SesiTanam::where('status', 'panen')
            ->with('tanaman')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($sesi) {
                $tanggalMulai = Carbon::parse($sesi->tanggal_mulai);
                $tanggalSelesai = Carbon::parse($sesi->updated_at);
                $durasi = max(1, $tanggalMulai->diffInDays($tanggalSelesai));
                
                // lampirkan perhitungan rentang waktu ke objek untuk ditampilkan di tampilan
                $sesi->durasi_hari = $durasi;
                
                return $sesi;
            });

        return view('pages.riwayat', compact('sesiAktif', 'sesiPanen'));
    }
}
