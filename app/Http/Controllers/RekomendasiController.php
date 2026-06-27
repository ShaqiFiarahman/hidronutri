<?php

namespace App\Http\Controllers;

use App\Models\Tanaman;
use Illuminate\Http\Request;
use App\Services\RuleBasedEngine;

class RekomendasiController extends Controller
{
    protected $engine;

    public function __construct(RuleBasedEngine $engine)
    {
        $this->engine = $engine;
    }

    public function index()
    {
        $tanaman = Tanaman::all();
        return view('pages.rekomendasi', compact('tanaman'));
    }

    public function proses(Request $request)
    {
        $validated = $request->validate([
            'tanaman_id' => 'required|exists:tanaman,id',
            'fase' => 'required|in:semai,vegetatif_awal,vegetatif_akhir,panen',
            'sistem_hidroponik' => 'required|in:nft,dft,rakit_apung,wick',
        ]);

        session([
            'rekomendasi_tanaman_id' => $validated['tanaman_id'],
            'rekomendasi_fase' => $validated['fase'],
            'rekomendasi_sistem' => $validated['sistem_hidroponik'],
        ]);

        return redirect('/hasil');
    }

    public function hasil()
    {
        $tanamanId = session('rekomendasi_tanaman_id');
        $fase = session('rekomendasi_fase');
        $sistem = session('rekomendasi_sistem');

        if (!$tanamanId || !$fase || !$sistem) {
            return redirect('/rekomendasi')->with('warning', 'Silakan pilih tanaman dan fase pertumbuhan terlebih dahulu.');
        }

        $tanaman = Tanaman::find($tanamanId);
        $rekomendasi = $this->engine->getRekomendasiNutrisi($tanamanId, $fase, $sistem);

        if (!$rekomendasi) {
            return redirect('/rekomendasi')->with('error', 'Aturan rekomendasi tidak ditemukan.');
        }

        return view('pages.hasil', compact('tanaman', 'fase', 'sistem', 'rekomendasi'));
    }
}
