<?php

namespace Database\Seeders;

use App\Models\Distribution;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $pelapor = User::updateOrCreate(
            ['email' => 'pelapor@mbg.test'],
            ['name' => 'Pelapor Demo', 'password' => 'Demo12345!']
        );
        $pelapor->role = 'pelapor';
        $pelapor->save();

        $pemantau = User::updateOrCreate(
            ['email' => 'pemantau@mbg.test'],
            ['name' => 'Pemantau Demo', 'password' => 'Demo12345!']
        );
        $pemantau->role = 'pemantau';
        $pemantau->save();

        $koordinator = User::updateOrCreate(
            ['email' => 'koordinator@mbg.test'],
            ['name' => 'Koordinator Demo', 'password' => 'Demo12345!']
        );
        $koordinator->role = 'koordinator';
        $koordinator->save();

        $sekolah = School::firstOrCreate(
            ['name' => 'SMP Contoh 1'],
            ['address' => 'Alamat Fiktif']
        );

        $distribusi = Distribution::firstOrCreate(
            [
                'school_id' => $sekolah->id,
                'distribution_date' => '2026-10-30',
            ],
            ['meal_description' => 'Menu contoh untuk data demo']
        );

        $sekolah2 = School::firstOrCreate(
            ['name' => 'SD Contoh 2'],
            ['address' => 'Alamat fiktif kedua']
        );

        $distribusi2 = Distribution::firstOrCreate(
            [
                'school_id' => $sekolah2->id,
                'distribution_date' => '2026-10-30',
            ],
            ['meal_description' => 'Menu contoh kedua']
        );

        $laporanDikirim = $pelapor->reports()->firstOrCreate(
            [
                'distribution_id' => $distribusi->id,
                'incident_at' => '2026-10-30 12:30:00',
                'description' => 'Laporan contoh yang baru dikirim.',
            ],
            ['people_affected' => 2]
        );
        $laporanDikirim->status = 'dikirim';
        $laporanDikirim->save();

        $laporanDitinjau = $pelapor->reports()->firstOrCreate(
            [
                'distribution_id' => $distribusi->id,
                'incident_at' => '2026-10-30 13:00:00',
                'description' => 'Laporan contoh yang perlu informasi tambahan.',
            ],
            ['people_affected' => 1]
        );
        $laporanDitinjau->status = 'perlu_perbaikan';
        $laporanDitinjau->review_note = 'Mohon lengkapi informasi waktu kejadian.';
        $laporanDitinjau->save();

        $laporanDiteruskan = $pelapor->reports()->firstOrCreate(
            [
                'distribution_id' => $distribusi->id,
                'incident_at' => '2026-10-30 14:00:00',
                'description' => 'Laporan contoh yang ditandai untuk tindak lanjut.',
            ],
            ['people_affected' => 3]
        );
        $laporanDiteruskan->status = 'diteruskan';
        $laporanDiteruskan->flagged_for_followup = true;
        $laporanDiteruskan->forwarded_to = 'Fasilitas kesehatan contoh';
        $laporanDiteruskan->forwarded_at = now();
        $laporanDiteruskan->forwarded_by = $koordinator->id;
        $laporanDiteruskan->save();

    }
}