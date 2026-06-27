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

        // Ambil daftar fase per tanaman dari rule_nutrisi
        $fasePerTanaman = \App\Models\RuleNutrisi::select('tanaman_id', 'fase')
            ->get()
            ->groupBy('tanaman_id')
            ->map(fn($rules) => $rules->pluck('fase')->values())
            ->toArray();

        // Map ID tanaman ke Nama untuk memudahkan JS
        $tanamanMap = $tanaman->pluck('nama', 'id')->toArray();

        // Ambil durasi map dari engine
        $durasiMap = $this->engine->getDurasiFaseMap();

        return view('pages.rekomendasi', compact('tanaman', 'fasePerTanaman', 'durasiMap', 'tanamanMap'));
    }

    public function proses(Request $request)
    {
        $validated = $request->validate([
            'tanaman_id' => 'required|exists:tanaman,id',
            'tanggal_mulai' => 'required|date|before_or_equal:today',
            'sistem_hidroponik' => 'required|in:nft,dft,rakit_apung,wick',
        ]);

        $tanaman = Tanaman::find($validated['tanaman_id']);
        $usiaHari = \Carbon\Carbon::parse($validated['tanggal_mulai'])->diffInDays(\Carbon\Carbon::today());
        
        // Tentukan fase secara dinamis
        $fase = $this->engine->determineFase($tanaman->nama, $usiaHari);

        // Validasi bahwa kombinasi tanaman + fase ada di rule_nutrisi
        $ruleExists = \App\Models\RuleNutrisi::where('tanaman_id', $validated['tanaman_id'])
            ->where('fase', $fase)
            ->exists();

        if (!$ruleExists) {
            return back()->withErrors(['tanaman_id' => 'Gagal menentukan fase yang valid untuk usia tanaman ini.'])->withInput();
        }

        session([
            'rekomendasi_tanaman_id' => $validated['tanaman_id'],
            'rekomendasi_fase' => $fase,
            'rekomendasi_sistem' => $validated['sistem_hidroponik'],
            'rekomendasi_tanggal_mulai' => $validated['tanggal_mulai'],
            'rekomendasi_usia_hari' => $usiaHari,
        ]);

        return redirect('/hasil');
    }

    public function hasil()
    {
        $tanamanId = session('rekomendasi_tanaman_id');
        $fase = session('rekomendasi_fase');
        $sistem = session('rekomendasi_sistem');
        $tanggalMulai = session('rekomendasi_tanggal_mulai');
        $usiaHari = session('rekomendasi_usia_hari');

        if (!$tanamanId || !$fase || !$sistem) {
            return redirect('/rekomendasi')->with('warning', 'Silakan pilih tanaman dan fase pertumbuhan terlebih dahulu.');
        }

        $tanaman = Tanaman::find($tanamanId);
        $rekomendasi = $this->engine->getRekomendasiNutrisi($tanamanId, $fase, $sistem);

        if (!$rekomendasi) {
            return redirect('/rekomendasi')->with('error', 'Aturan rekomendasi tidak ditemukan.');
        }

        return view('pages.hasil', compact('tanaman', 'fase', 'sistem', 'rekomendasi', 'tanggalMulai', 'usiaHari'));
    }
}
