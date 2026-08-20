<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KehadiranGuru;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\User;

class KehadiranGuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get class IDs for XII RPL 1 and XII TKJ 1
        $kelasRPL = \App\Models\Kelas::where('nama', 'XII RPL 1')->first();
        $kelasTKJ = \App\Models\Kelas::where('nama', 'XII TKJ 1')->first();

        if (!$kelasRPL || !$kelasTKJ) {
            $this->command->error('❌ Kelas XII RPL 1 or XII TKJ 1 not found. Please run KelasSeeder first.');
            return;
        }

        // Get existing gurus, jadwals related to these classes, and admin users for foreign key references
        $gurus = Guru::pluck('id')->toArray();
        // Get jadwals that belong to the specific classes
        $jadwals = \App\Models\Jadwal::whereHas('kelas', function($query) use ($kelasRPL, $kelasTKJ) {
            $query->whereIn('id', [$kelasRPL->id, $kelasTKJ->id]);
        })->pluck('id')->toArray();

        // If no jadwals found for these classes, use all jadwals as fallback
        if (empty($jadwals)) {
            $jadwals = \App\Models\Jadwal::pluck('id')->toArray();
            $this->command->info('ℹ️ No jadwals found for XII RPL 1 or XII TKJ 1. Using all jadwals as fallback.');
        }

        $adminUsers = User::where('role', 'admin')->pluck('id')->toArray();

        $kehadiranGuruData = [
            [
                'jadwal_id' => $jadwals[array_rand($jadwals)],
                'guru_id' => $gurus[array_rand($gurus)],
                'tanggal' => now()->subDays(5)->toDateString(),
                'status_kehadiran' => 'hadir',
                'waktu_datang' => '07:00:00',
                'keterangan' => 'Hadir tepat waktu',
                'diinput_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
            ],
            [
                'jadwal_id' => $jadwals[array_rand($jadwals)],
                'guru_id' => $gurus[array_rand($gurus)],
                'tanggal' => now()->subDays(4)->toDateString(),
                'status_kehadiran' => 'telat',
                'waktu_datang' => '07:45:00',
                'durasi_keterlambatan' => 45,
                'keterangan' => 'Terlambat karena kendala lalu lintas',
                'diinput_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
            ],
            [
                'jadwal_id' => $jadwals[array_rand($jadwals)],
                'guru_id' => $gurus[array_rand($gurus)],
                'tanggal' => now()->subDays(3)->toDateString(),
                'status_kehadiran' => 'izin',
                'keterangan' => 'Izin acara keluarga',
                'diinput_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
            ],
            [
                'jadwal_id' => $jadwals[array_rand($jadwals)],
                'guru_id' => $gurus[array_rand($gurus)],
                'tanggal' => now()->subDays(2)->toDateString(),
                'status_kehadiran' => 'sakit',
                'keterangan' => 'Sakit demam',
                'diinput_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
            ],
            [
                'jadwal_id' => $jadwals[array_rand($jadwals)],
                'guru_id' => $gurus[array_rand($gurus)],
                'tanggal' => now()->subDays(1)->toDateString(),
                'status_kehadiran' => 'hadir',
                'waktu_datang' => '06:55:00',
                'keterangan' => 'Hadir lebih awal',
                'diinput_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
            ],
            [
                'jadwal_id' => $jadwals[array_rand($jadwals)],
                'guru_id' => $gurus[array_rand($gurus)],
                'tanggal' => now()->subDays(6)->toDateString(),
                'status_kehadiran' => 'telat',
                'waktu_datang' => '08:15:00',
                'durasi_keterlambatan' => 75,
                'keterangan' => 'Kendala transportasi',
                'diinput_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
            ],
            [
                'jadwal_id' => $jadwals[array_rand($jadwals)],
                'guru_id' => $gurus[array_rand($gurus)],
                'tanggal' => now()->subDays(7)->toDateString(),
                'status_kehadiran' => 'hadir',
                'waktu_datang' => '07:05:00',
                'keterangan' => 'Hadir tepat waktu',
                'diinput_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
            ],
            [
                'jadwal_id' => $jadwals[array_rand($jadwals)],
                'guru_id' => $gurus[array_rand($gurus)],
                'tanggal' => now()->subDays(8)->toDateString(),
                'status_kehadiran' => 'tidak_hadir',
                'keterangan' => 'Tanpa keterangan',
                'diinput_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
            ],
            [
                'jadwal_id' => $jadwals[array_rand($jadwals)],
                'guru_id' => $gurus[array_rand($gurus)],
                'tanggal' => now()->subDays(9)->toDateString(),
                'status_kehadiran' => 'hadir',
                'waktu_datang' => '07:10:00',
                'keterangan' => 'Hadir dengan sedikit keterlambatan',
                'diinput_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
            ],
            [
                'jadwal_id' => $jadwals[array_rand($jadwals)],
                'guru_id' => $gurus[array_rand($gurus)],
                'tanggal' => now()->subDays(10)->toDateString(),
                'status_kehadiran' => 'sakit',
                'keterangan' => 'Sakit berobat',
                'diinput_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
            ],
        ];

        foreach ($kehadiranGuruData as $data) {
            KehadiranGuru::create($data);
        }

        $this->command->info('✅ ' . count($kehadiranGuruData) . ' Kehadiran Guru records for schedules related to XII RPL 1 and XII TKJ 1 seeded successfully!');
    }
}