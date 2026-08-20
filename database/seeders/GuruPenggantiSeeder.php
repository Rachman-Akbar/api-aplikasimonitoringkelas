<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GuruPengganti;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\User;

class GuruPenggantiSeeder extends Seeder
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

        // Ensure we have at least some entries for each class
        $rplJadwals = \App\Models\Jadwal::where('kelas_id', $kelasRPL->id)->pluck('id')->toArray();
        $tkjJadwals = \App\Models\Jadwal::where('kelas_id', $kelasTKJ->id)->pluck('id')->toArray();

        // If there are no specific jadwals for these classes, fall back to the general set
        $rplJadwals = !empty($rplJadwals) ? $rplJadwals : $jadwals;
        $tkjJadwals = !empty($tkjJadwals) ? $tkjJadwals : $jadwals;

        $guruPenggantiData = [
            [
                'jadwal_id' => $rplJadwals[array_rand($rplJadwals)],
                'tanggal' => now()->subDays(5),
                'guru_asli_id' => $gurus[array_rand($gurus)],
                'guru_pengganti_id' => $gurus[array_rand($gurus)],
                'keterangan' => 'Guru asli sakit',
                'status_penggantian' => 'selesai',
                'disetujui_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
            ],
            [
                'jadwal_id' => $tkjJadwals[array_rand($tkjJadwals)],
                'tanggal' => now()->subDays(4),
                'guru_asli_id' => $gurus[array_rand($gurus)],
                'guru_pengganti_id' => $gurus[array_rand($gurus)],
                'keterangan' => 'Dinas luar',
                'status_penggantian' => 'dijadwalkan',
                'disetujui_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
            ],
            [
                'jadwal_id' => $rplJadwals[array_rand($rplJadwals)],
                'tanggal' => now()->subDays(10),
                'guru_asli_id' => $gurus[array_rand($gurus)],
                'guru_pengganti_id' => $gurus[array_rand($gurus)],
                'keterangan' => 'Izin acara keluarga',
                'status_penggantian' => 'selesai',
                'disetujui_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
            ],
            [
                'jadwal_id' => $tkjJadwals[array_rand($tkjJadwals)],
                'tanggal' => now()->subDays(8),
                'guru_asli_id' => $gurus[array_rand($gurus)],
                'guru_pengganti_id' => $gurus[array_rand($gurus)],
                'keterangan' => 'Sakit berobat',
                'status_penggantian' => 'selesai',
                'disetujui_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
            ],
            [
                'jadwal_id' => $rplJadwals[array_rand($rplJadwals)],
                'tanggal' => now()->subDays(15),
                'guru_asli_id' => $gurus[array_rand($gurus)],
                'guru_pengganti_id' => $gurus[array_rand($gurus)],
                'keterangan' => 'Menghadiri pelatihan',
                'status_penggantian' => 'dibatalkan',
                'disetujui_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
            ],
            [
                'jadwal_id' => $tkjJadwals[array_rand($tkjJadwals)],
                'tanggal' => now()->subDays(3),
                'guru_asli_id' => $gurus[array_rand($gurus)],
                'guru_pengganti_id' => $gurus[array_rand($gurus)],
                'keterangan' => 'Izin pribadi',
                'status_penggantian' => 'dijadwalkan',
                'disetujui_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
            ],
            [
                'jadwal_id' => $rplJadwals[array_rand($rplJadwals)],
                'tanggal' => now()->subDays(12),
                'guru_asli_id' => $gurus[array_rand($gurus)],
                'guru_pengganti_id' => $gurus[array_rand($gurus)],
                'keterangan' => 'Acara resmi',
                'status_penggantian' => 'selesai',
                'disetujui_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
            ],
            [
                'jadwal_id' => $tkjJadwals[array_rand($tkjJadwals)],
                'tanggal' => now()->subDays(7),
                'guru_asli_id' => $gurus[array_rand($gurus)],
                'guru_pengganti_id' => $gurus[array_rand($gurus)],
                'keterangan' => 'Dinas ke luar kota',
                'status_penggantian' => 'selesai',
                'disetujui_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
            ],
            [
                'jadwal_id' => $rplJadwals[array_rand($rplJadwals)],
                'tanggal' => now()->addDays(1),
                'guru_asli_id' => $gurus[array_rand($gurus)],
                'guru_pengganti_id' => $gurus[array_rand($gurus)],
                'keterangan' => 'Keperluan keluarga',
                'status_penggantian' => 'dijadwalkan',
                'disetujui_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
            ],
            [
                'jadwal_id' => $tkjJadwals[array_rand($tkjJadwals)],
                'tanggal' => now()->subDays(20),
                'guru_asli_id' => $gurus[array_rand($gurus)],
                'guru_pengganti_id' => $gurus[array_rand($gurus)],
                'keterangan' => 'Sakit demam',
                'status_penggantian' => 'selesai',
                'disetujui_oleh' => $adminUsers[array_rand($adminUsers)] ?? null,
            ],
        ];

        foreach ($guruPenggantiData as $data) {
            // Ensure guru_asli_id and guru_pengganti_id are different
            while ($data['guru_asli_id'] == $data['guru_pengganti_id']) {
                $data['guru_pengganti_id'] = $gurus[array_rand($gurus)];
            }

            GuruPengganti::create($data);
        }

        $this->command->info('✅ ' . count($guruPenggantiData) . ' Guru Pengganti records for schedules related to XII RPL 1 and XII TKJ 1 seeded successfully!');
    }
}