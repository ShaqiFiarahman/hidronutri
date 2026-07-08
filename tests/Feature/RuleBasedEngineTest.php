<?php

namespace Tests\Feature;

use App\Models\RuleNutrisi;
use App\Models\Tanaman;
use App\Services\RuleBasedEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RuleBasedEngineTest extends TestCase
{
    use RefreshDatabase;

    protected $engine;
    protected $tanaman;
    protected $rule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = new RuleBasedEngine();

        // Buat data tanaman
        $this->tanaman = Tanaman::create([
            'nama' => 'Cabai',
            'emoji' => '🌶️',
            'kategori' => 'buah',
        ]);

        // Buat data rule nutrisi
        $this->rule = RuleNutrisi::create([
            'tanaman_id' => $this->tanaman->id,
            'fase' => 'vegetatif_awal',
            'ph_min' => 5.5,
            'ph_max' => 6.5,
            'ec_min' => 1.2,
            'ec_max' => 1.8,
            'ppm_min' => 600,
            'ppm_max' => 800,
            'dosis_a' => 5.0,
            'dosis_b' => 5.0,
            'ganti_larutan' => 14,
        ]);
    }

    /**
     * Test pH Rendah (Normal/Non-Ekstrem) dan Ekstrem (< 4.0)
     */
    public function test_diagnosa_ph_rendah_dan_ekstrem(): void
    {
        $suffix = " Setelah setiap tindakan korektif, aduk larutan hingga tercampur merata, tunggu sirkulasi stabil, ukur kembali pH, EC, dan PPM, baru lakukan tindakan berikutnya apabila masih diperlukan.";

        // pH Rendah Normal (5.0, di bawah ph_min 5.5)
        $hasil = $this->engine->diagnosaAbnormal($this->tanaman->id, 'vegetatif_awal', 5.0, 1.4, 700);

        $this->assertCount(1, $hasil);
        $this->assertEquals('pH', $hasil[0]['parameter']);
        $this->assertEquals('rendah', $hasil[0]['kondisi']);
        $this->assertEquals(
            "Pastikan terlebih dahulu nutrisi (A dan B) telah disesuaikan hingga target PPM tercapai. Aduk larutan hingga tercampur merata. Ukur kembali pH. Apabila pH masih berada di luar rentang ideal, barulah gunakan pH Up sedikit demi sedikit." . $suffix,
            $hasil[0]['tindakan']
        );

        // pH Rendah Ekstrem (3.9, kurang dari 4.0)
        $hasilEkstrem = $this->engine->diagnosaAbnormal($this->tanaman->id, 'vegetatif_awal', 3.9, 1.4, 700);
        $this->assertCount(1, $hasilEkstrem);
        $this->assertEquals(
            "Disarankan mengganti sumber air baku dengan air yang memiliki pH lebih netral sebelum melakukan penyesuaian pH." . $suffix,
            $hasilEkstrem[0]['tindakan']
        );
    }

    /**
     * Test pH Tinggi (Normal/Non-Ekstrem) dan Ekstrem (> 8.0)
     */
    public function test_diagnosa_ph_tinggi_dan_ekstrem(): void
    {
        $suffix = " Setelah setiap tindakan korektif, aduk larutan hingga tercampur merata, tunggu sirkulasi stabil, ukur kembali pH, EC, dan PPM, baru lakukan tindakan berikutnya apabila masih diperlukan.";

        // pH Tinggi Normal (7.0, di atas ph_max 6.5)
        $hasil = $this->engine->diagnosaAbnormal($this->tanaman->id, 'vegetatif_awal', 7.0, 1.4, 700);

        $this->assertCount(1, $hasil);
        $this->assertEquals('pH', $hasil[0]['parameter']);
        $this->assertEquals('tinggi', $hasil[0]['kondisi']);
        $this->assertEquals(
            "Pastikan terlebih dahulu nutrisi (A dan B) telah disesuaikan hingga target PPM tercapai. Aduk larutan hingga tercampur merata. Ukur kembali pH. Apabila pH masih berada di luar rentang ideal, barulah gunakan pH Down sedikit demi sedikit." . $suffix,
            $hasil[0]['tindakan']
        );

        // pH Tinggi Ekstrem (8.1, lebih dari 8.0)
        $hasilEkstrem = $this->engine->diagnosaAbnormal($this->tanaman->id, 'vegetatif_awal', 8.1, 1.4, 700);
        $this->assertCount(1, $hasilEkstrem);
        $this->assertEquals(
            "Disarankan mengganti sumber air baku dengan air yang memiliki pH lebih netral sebelum melakukan penyesuaian pH." . $suffix,
            $hasilEkstrem[0]['tindakan']
        );
    }

    /**
     * Test PPM Tinggi
     */
    public function test_diagnosa_ppm_tinggi(): void
    {
        $suffix = " Setelah setiap tindakan korektif, aduk larutan hingga tercampur merata, tunggu sirkulasi stabil, ukur kembali pH, EC, dan PPM, baru lakukan tindakan berikutnya apabila masih diperlukan.";

        $hasil = $this->engine->diagnosaAbnormal($this->tanaman->id, 'vegetatif_awal', 6.0, 1.8, 900);

        $this->assertCount(1, $hasil);
        $this->assertEquals('PPM', $hasil[0]['parameter']);
        $this->assertEquals('tinggi', $hasil[0]['kondisi']);
        $this->assertEquals(
            "Prioritaskan penambahan air baku hingga PPM mendekati target. Penggantian larutan hanya direkomendasikan apabila kondisi benar-benar ekstrem." . $suffix,
            $hasil[0]['tindakan']
        );
    }

    /**
     * Test PPM Rendah
     */
    public function test_diagnosa_ppm_rendah(): void
    {
        $suffix = " Setelah setiap tindakan korektif, aduk larutan hingga tercampur merata, tunggu sirkulasi stabil, ukur kembali pH, EC, dan PPM, baru lakukan tindakan berikutnya apabila masih diperlukan.";

        $hasil = $this->engine->diagnosaAbnormal($this->tanaman->id, 'vegetatif_awal', 6.0, 1.0, 500);

        $this->assertCount(1, $hasil);
        $this->assertEquals('PPM', $hasil[0]['parameter']);
        $this->assertEquals('rendah', $hasil[0]['kondisi']);
        $this->assertEquals(
            "Tambahkan larutan nutrisi AB Mix secara bertahap sedikit demi sedikit, kemudian ukur kembali hingga target PPM tercapai." . $suffix,
            $hasil[0]['tindakan']
        );
    }
}
