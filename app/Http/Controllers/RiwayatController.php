<?php

namespace App\Http\Controllers;

use App\Models\SesiTanam;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RiwayatController extends Controller
{
    public function index()
    {
        $sesiAktif = SesiTanam::where('status', 'aktif')
            ->with('tanaman')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($sesi) {
                $tanggalMulai = Carbon::parse($sesi->tanggal_mulai);
                $usiaHari = max(0, $tanggalMulai->diffInDays(Carbon::today()));
                
                // Tambahkan atribut dinamis
                $sesi->usia_hari = $usiaHari;
                
                $durasiTotal = 35; // Estimasi total hari sampai panen
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
                
                // Tambahkan atribut dinamis
                $sesi->durasi_hari = $durasi;
                
                return $sesi;
            });

        return view('pages.riwayat', compact('sesiAktif', 'sesiPanen'));
    }
}
