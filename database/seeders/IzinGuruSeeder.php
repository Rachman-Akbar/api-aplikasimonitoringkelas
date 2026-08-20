<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IzinGuru;
use App\Models\Guru;
use App\Models\User;

class IzinGuruSeeder extends Seeder
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

        // Get existing gurus and admin users for foreign key references
        $gurus = Guru::pluck('id')->toArray();
        $adminUsers = User::where('role', 'admin')->pluck('id')->toArray();

        // Get teachers who teach in the target classes
        $guruIdsRPL = \App\Models\Jadwal::where('kelas_id', $kelasRPL->id)->pluck('guru_id')->toArray();
        $guruIdsTKJ = \App\Models\Jadwal::where('kelas_id', $kelasTKJ->id)->pluck('guru_id')->toArray();
        $guruIdsTarget = array_unique(array_merge($guruIdsRPL, $guruIdsTKJ));

        // If no specific teachers found in target classes, use available gurus as fallback
        if (empty($guruIdsTarget)) {
            $guruIdsTarget = $gurus;
            $this->command->info('ℹ️ No teachers found in target classes (XII RPL 1, XII TKJ 1). Using all teachers as fallback.');
        }

        $izinGuruData = [
            [
                'guru_id' => $guruIdsTarget[array_rand($guruIdsTarget)],
                'tanggal_mulai' => now()->subDays(5),
                'tanggal_selesai' => now()->subDays(3),
                'durasi_hari' => 2,
                'jenis_izin' => 'sakit',
                'keterangan' => 'Sakit demam tinggi',
                'status_approval' => 'disetujui',
                'disetujui_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
                'tanggal_approval' => now()->subDays(6),
            ],
            [
                'guru_id' => $guruIdsTarget[array_rand($guruIdsTarget)],
                'tanggal_mulai' => now()->subDays(10),
                'tanggal_selesai' => now()->subDays(9),
                'durasi_hari' => 1,
                'jenis_izin' => 'izin',
                'keterangan' => 'Acara keluarga',
                'status_approval' => 'disetujui',
                'disetujui_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
                'tanggal_approval' => now()->subDays(11),
            ],
            [
                'guru_id' => $guruIdsTarget[array_rand($guruIdsTarget)],
                'tanggal_mulai' => now()->subDays(15),
                'tanggal_selesai' => now()->subDays(15),
                'durasi_hari' => 0,
                'jenis_izin' => 'izin',
                'keterangan' => 'Keperluan pribadi',
                'status_approval' => 'ditolak',
                'disetujui_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
                'tanggal_approval' => now()->subDays(16),
            ],
            [
                'guru_id' => $gurus[array_rand($gurus)],
                'tanggal_mulai' => now()->addDays(2),
                'tanggal_selesai' => now()->addDays(4),
                'durasi_hari' => 2,
                'jenis_izin' => 'sakit',
                'keterangan' => 'Sakit berobat',
                'status_approval' => 'pending',
            ],
            [
                'guru_id' => $guruIdsTarget[array_rand($guruIdsTarget)],
                'tanggal_mulai' => now()->subDays(20),
                'tanggal_selesai' => now()->subDays(18),
                'durasi_hari' => 2,
                'jenis_izin' => 'cuti',
                'keterangan' => 'Cuti tahunan',
                'status_approval' => 'disetujui',
                'disetujui_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
                'tanggal_approval' => now()->subDays(21),
            ],
            [
                'guru_id' => $guruIdsTarget[array_rand($guruIdsTarget)],
                'tanggal_mulai' => now()->subDays(8),
                'tanggal_selesai' => now()->subDays(7),
                'durasi_hari' => 1,
                'jenis_izin' => 'dinas_luar',
                'keterangan' => 'Dinas ke luar kota',
                'status_approval' => 'disetujui',
                'disetujui_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
                'tanggal_approval' => now()->subDays(9),
            ],
            [
                'guru_id' => $guruIdsTarget[array_rand($guruIdsTarget)],
                'tanggal_mulai' => now()->subDays(12),
                'tanggal_selesai' => now()->subDays(12),
                'durasi_hari' => 0,
                'jenis_izin' => 'izin',
                'keterangan' => 'Acara pernikahan keluarga',
                'status_approval' => 'disetujui',
                'disetujui_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
                'tanggal_approval' => now()->subDays(13),
            ],
            [
                'guru_id' => $guruIdsTarget[array_rand($guruIdsTarget)],
                'tanggal_mulai' => now()->subDays(25),
                'tanggal_selesai' => now()->subDays(24),
                'durasi_hari' => 1,
                'jenis_izin' => 'sakit',
                'keterangan' => 'Sakit gigi',
                'status_approval' => 'disetujui',
                'disetujui_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
                'tanggal_approval' => now()->subDays(26),
            ],
            [
                'guru_id' => $guruIdsTarget[array_rand($guruIdsTarget)],
                'tanggal_mulai' => now()->subDays(30),
                'tanggal_selesai' => now()->subDays(29),
                'durasi_hari' => 1,
                'jenis_izin' => 'izin',
                'keterangan' => 'Menghadiri undangan resmi',
                'status_approval' => 'disetujui',
                'disetujui_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
                'tanggal_approval' => now()->subDays(31),
            ],
            [
                'guru_id' => $gurus[array_rand($gurus)],
                'tanggal_mulai' => now()->subDays(3),
                'tanggal_selesai' => now()->subDays(1),
                'durasi_hari' => 2,
                'jenis_izin' => 'cuti',
                'keterangan' => 'Cuti melahirkan',
                'status_approval' => 'pending',
            ],
        ];

        foreach ($izinGuruData as $data) {
            IzinGuru::create($data);
        }

        $this->command->info('✅ ' . count($izinGuruData) . ' Izin Guru records seeded successfully, with priority given to teachers of XII RPL 1 and XII TKJ 1 classes!');
    }
}