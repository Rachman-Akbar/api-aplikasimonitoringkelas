<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\MataPelajaran;

class JadwalTestSeeder extends Seeder
{
    public function run()
    {
        $kelas = Kelas::first();
        $guru = Guru::first();
        $mapel = MataPelajaran::first();

        if (!$kelas || !$guru || !$mapel) {
            echo "Data kelas, guru, atau mata pelajaran tidak ditemukan!" . PHP_EOL;
            return;
        }

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        foreach ($hariList as $index => $hari) {
            // Check if the record already exists
            $existing = Jadwal::where([
                'kelas_id' => $kelas->id,
                'hari' => $hari,
                'jam_ke' => $index + 1
            ])->first();
            
            if (!$existing) {
                Jadwal::create([
                    'kelas_id' => $kelas->id,
                    'mata_pelajaran_id' => $mapel->id,
                    'guru_id' => $guru->id,
                    'hari' => $hari,
                    'jam_ke' => $index + 1,
                    'jam_mulai' => sprintf('%02d:00', 7 + $index),
                    'jam_selesai' => sprintf('%02d:30', 7 + $index),
                    'ruangan' => 'R' . ($index + 1),
                    'status' => 'aktif'
                ]);
            }
        }

        echo 'Jadwal berhasil ditambahkan! Total: ' . Jadwal::count() . PHP_EOL;
    }
}