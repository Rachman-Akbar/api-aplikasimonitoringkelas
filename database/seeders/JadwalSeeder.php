<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Guru;
use Carbon\Carbon;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📅 Creating 10 schedule entries...');

        // Get IDs from existing data
        $kelasIds = Kelas::pluck('id')->toArray();
        $mataPelajaranIds = MataPelajaran::pluck('id')->toArray();
        $guruIds = Guru::pluck('id')->toArray();

        if (empty($kelasIds) || empty($mataPelajaranIds) || empty($guruIds)) {
            $this->command->error('❌ Missing required data: classes, subjects, or teachers. Please run other seeders first.');
            return;
        }

        $hariOptions = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jamOptions = [
            ['mulai' => '07:00', 'selesai' => '08:40', 'ke' => 1],
            ['mulai' => '08:40', 'selesai' => '10:20', 'ke' => 2],
            ['mulai' => '10:20', 'selesai' => '12:00', 'ke' => 3],
            ['mulai' => '13:00', 'selesai' => '14:40', 'ke' => 4],
            ['mulai' => '14:40', 'selesai' => '16:20', 'ke' => 5],
            ['mulai' => '16:20', 'selesai' => '18:00', 'ke' => 6]
        ];

        $jadwalData = [];

        for ($i = 0; $i < 10; $i++) {
            $kelasId = $kelasIds[array_rand($kelasIds)];
            $mataPelajaranId = $mataPelajaranIds[array_rand($mataPelajaranIds)];
            $guruId = $guruIds[array_rand($guruIds)];
            $hari = $hariOptions[array_rand($hariOptions)];
            $jam = $jamOptions[array_rand($jamOptions)];

            $jadwalData[] = [
                'kelas_id' => $kelasId,
                'mata_pelajaran_id' => $mataPelajaranId,
                'guru_id' => $guruId,
                'hari' => $hari,
                'jam_ke' => $jam['ke'],
                'jam_mulai' => $jam['mulai'],
                'jam_selesai' => $jam['selesai'],
                'ruangan' => 'Ruang ' . rand(100, 199),
                'keterangan' => 'Jadwal pelajaran reguler',
                'status' => 'aktif',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }

        foreach ($jadwalData as $jadwal) {
            Jadwal::create($jadwal);
        }

        $this->command->info('✅ Successfully created 10 schedule entries');
    }
}