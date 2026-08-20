<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Jadwal;
use App\Models\IzinGuru;
use App\Models\Kehadiran;
use App\Models\KehadiranGuru;
use App\Models\GuruPengganti;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CompleteDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Memulai seeding data...');

        // 1. Create Users untuk Guru yang sudah ada
        $this->seedUserGuru();

        // 2. Create Users untuk Siswa yang sudah ada
        $this->seedUserSiswa();

        // 3. Seed Kehadiran Siswa
        $this->seedKehadiranSiswa();

        // 4. Seed Izin Guru
        $this->seedIzinGuru();

        // 5. Seed Kehadiran Guru
        $this->seedKehadiranGuru();

        // 6. Seed Guru Pengganti
        $this->seedGuruPengganti();

        $this->command->info('✅ Seeding selesai!');
    }

    private function seedUserGuru()
    {
        $this->command->info('📝 Membuat user untuk guru...');

        $gurus = Guru::whereNull('user_id')->get();

        foreach ($gurus as $index => $guru) {
            // Skip jika guru tidak punya email
            if (empty($guru->email)) {
                continue;
            }

            // Cek apakah email sudah digunakan
            $existingUser = User::where('email', $guru->email)->first();
            if ($existingUser) {
                $guru->update(['user_id' => $existingUser->id]);
                $existingUser->update(['guru_id' => $guru->id]);
                continue;
            }

            // Create user baru
            $user = User::create([
                'name' => $guru->nama,
                'email' => $guru->email,
                'password' => Hash::make('password123'), // Default password
                'role' => 'guru',
                'guru_id' => $guru->id,
            ]);

            // Update guru dengan user_id
            $guru->update(['user_id' => $user->id]);

            $this->command->info("  ✓ User created: {$guru->nama} ({$guru->email})");
        }
    }

    private function seedUserSiswa()
    {
        $this->command->info('📝 Membuat user untuk siswa...');

        $siswas = Siswa::whereNull('user_id')->take(20)->get(); // Ambil 20 siswa

        foreach ($siswas as $siswa) {
            // Generate email jika belum ada
            $email = $siswa->email ?? strtolower(str_replace(' ', '.', $siswa->nama)) . '@siswa.sch.id';

            // Cek apakah email sudah digunakan
            $existingUser = User::where('email', $email)->first();
            if ($existingUser) {
                $siswa->update(['user_id' => $existingUser->id]);
                continue;
            }

            // Create user baru
            $user = User::create([
                'name' => $siswa->nama,
                'email' => $email,
                'password' => Hash::make('password123'), // Default password
                'role' => 'siswa',
                'kelas_id' => $siswa->kelas_id,
            ]);

            // Update siswa dengan user_id
            $siswa->update(['user_id' => $user->id]);

            $this->command->info("  ✓ User created: {$siswa->nama} ({$email})");
        }
    }

    private function seedKehadiranSiswa()
    {
        $this->command->info('📝 Membuat data kehadiran siswa...');

        $jadwals = Jadwal::with('kelas.siswas')->take(10)->get();
        $startDate = Carbon::now()->subDays(7); // 1 minggu terakhir

        $count = 0;
        foreach ($jadwals as $jadwal) {
            $siswas = $jadwal->kelas->siswas;

            for ($i = 0; $i < 5; $i++) {
                $tanggal = $startDate->copy()->addDays($i);

                // Skip jika bukan hari jadwal
                if ($tanggal->locale('id')->isoFormat('dddd') !== $jadwal->hari) {
                    continue;
                }

                foreach ($siswas as $siswa) {
                    // Random status
                    $status = $this->getRandomStatus(['hadir' => 85, 'sakit' => 7, 'izin' => 5, 'tidak_hadir' => 3]);

                    $keterangan = null;
                    if ($status === 'sakit') {
                        $keterangan = 'Sakit flu';
                    } elseif ($status === 'izin') {
                        $keterangan = 'Izin keperluan keluarga';
                    }

                    Kehadiran::updateOrCreate(
                        [
                            'siswa_id' => $siswa->id,
                            'jadwal_id' => $jadwal->id,
                            'tanggal' => $tanggal->format('Y-m-d'),
                        ],
                        [
                            'status' => $status,
                            'keterangan' => $keterangan,
                        ]
                    );
                    $count++;
                }
            }
        }

        $this->command->info("  ✓ {$count} data kehadiran siswa dibuat");
    }

    private function seedIzinGuru()
    {
        $this->command->info('📝 Membuat data izin guru...');

        $gurus = Guru::take(5)->get();

        foreach ($gurus as $guru) {
            // Izin yang sudah disetujui (masa lalu)
            $tanggalMulai = Carbon::now()->subDays(10);
            $tanggalSelesai = Carbon::now()->subDays(8);

            IzinGuru::create([
                'guru_id' => $guru->id,
                'jenis_izin' => 'sakit',
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'durasi_hari' => $tanggalMulai->diffInDays($tanggalSelesai) + 1,
                'keterangan' => 'Sakit demam tinggi',
                'file_surat' => '/storage/surat/izin_' . $guru->id . '_001.pdf',
                'status_approval' => 'disetujui',
                'disetujui_oleh' => 1,
                'tanggal_approval' => Carbon::now()->subDays(11),
            ]);

            // Izin pending (saat ini)
            $tanggalMulai2 = Carbon::now()->addDays(3);
            $tanggalSelesai2 = Carbon::now()->addDays(3);

            IzinGuru::create([
                'guru_id' => $guru->id,
                'jenis_izin' => 'izin',
                'tanggal_mulai' => $tanggalMulai2,
                'tanggal_selesai' => $tanggalSelesai2,
                'durasi_hari' => $tanggalMulai2->diffInDays($tanggalSelesai2) + 1,
                'keterangan' => 'Keperluan keluarga',
                'file_surat' => '/storage/surat/izin_' . $guru->id . '_002.pdf',
                'status_approval' => 'pending',
            ]);
        }

        $this->command->info('  ✓ ' . IzinGuru::count() . ' data izin guru dibuat');
    }

    private function seedKehadiranGuru()
    {
        $this->command->info('📝 Membuat data kehadiran guru...');

        $jadwals = Jadwal::with('guru')->take(20)->get();
        $startDate = Carbon::now()->subDays(14); // 2 minggu terakhir
        $adminUser = User::where('role', 'admin')->first() ?? User::first();

        $count = 0;
        foreach ($jadwals as $jadwal) {
            if (!$jadwal->guru) {
                continue;
            }

            for ($i = 0; $i < 10; $i++) {
                $tanggal = $startDate->copy()->addDays($i);

                // Skip jika bukan hari jadwal
                $hariIndo = $tanggal->locale('id')->isoFormat('dddd');
                if ($hariIndo !== $jadwal->hari) {
                    continue;
                }

                // Random status dengan bobot
                $rand = mt_rand(1, 100);
                if ($rand <= 75) {
                    $status = 'hadir';
                    $waktuDatang = '07:00:00 AM';
                    $durasi = 0;
                } elseif ($rand <= 85) {
                    $status = 'telat';
                    $waktuDatang = '07:' . mt_rand(15, 45) . ':00 AM';
                    $durasi = mt_rand(15, 45);
                } elseif ($rand <= 92) {
                    $status = 'izin';
                    $waktuDatang = null;
                    $durasi = null;
                } elseif ($rand <= 97) {
                    $status = 'sakit';
                    $waktuDatang = null;
                    $durasi = null;
                } else {
                    $status = 'tidak_hadir';
                    $waktuDatang = null;
                    $durasi = null;
                }

                $keterangan = match ($status) {
                    'telat' => 'Terlambat karena macet',
                    'izin' => 'Izin keperluan keluarga',
                    'sakit' => 'Sakit flu',
                    'tidak_hadir' => 'Tanpa keterangan',
                    default => null,
                };

                KehadiranGuru::updateOrCreate(
                    [
                        'jadwal_id' => $jadwal->id,
                        'guru_id' => $jadwal->guru_id,
                        'tanggal' => $tanggal->format('Y-m-d'),
                    ],
                    [
                        'status_kehadiran' => $status,
                        'waktu_datang' => $waktuDatang,
                        'durasi_keterlambatan' => $durasi,
                        'keterangan' => $keterangan,
                        'diinput_oleh' => $adminUser->id,
                    ]
                );
                $count++;
            }
        }

        $this->command->info("  ✓ {$count} data kehadiran guru dibuat");
    }

    private function seedGuruPengganti()
    {
        $this->command->info('📝 Membuat data guru pengganti...');

        // Ambil kehadiran guru yang izin, sakit, atau tidak hadir
        $kehadiranGurus = KehadiranGuru::whereIn('status_kehadiran', ['izin', 'sakit', 'tidak_hadir'])
            ->with('jadwal.guru')
            ->take(10)
            ->get();

        $adminUser = User::where('role', 'admin')->first() ?? User::first();
        $guruPenggantiList = Guru::take(5)->get();

        $count = 0;
        foreach ($kehadiranGurus as $kehadiran) {
            if (!$kehadiran->jadwal || !$kehadiran->jadwal->guru) {
                continue;
            }

            // Pilih guru pengganti random (bukan guru asli)
            $guruPengganti = $guruPenggantiList
                ->where('id', '!=', $kehadiran->guru_id)
                ->random();

            // Status: dijadwalkan, selesai, dibatalkan (sesuai migration)
            $statusArray = ['dijadwalkan', 'selesai', 'dibatalkan'];
            $status = $statusArray[array_rand($statusArray)];

            $alasan = match ($kehadiran->status_kehadiran) {
                'sakit' => 'Guru asli sedang sakit',
                'izin' => 'Guru asli sedang izin',
                'tidak_hadir' => 'Guru asli tidak hadir',
                default => 'Penggantian guru',
            };

            GuruPengganti::updateOrCreate(
                [
                    'jadwal_id' => $kehadiran->jadwal_id,
                    'tanggal' => $kehadiran->tanggal,
                    'guru_asli_id' => $kehadiran->guru_id,
                ],
                [
                    'guru_pengganti_id' => $guruPengganti->id,
                    'alasan_penggantian' => $alasan,
                    'status_penggantian' => $status,
                    'keterangan' => $status === 'dibatalkan' ? 'Tidak tersedia' : null,
                    'dibuat_oleh' => $adminUser->id,
                ]
            );
            $count++;
        }

        $this->command->info("  ✓ {$count} data guru pengganti dibuat");
    }

    private function getRandomStatus($weights)
    {
        $rand = mt_rand(1, 100);
        $sum = 0;

        foreach ($weights as $status => $weight) {
            $sum += $weight;
            if ($rand <= $sum) {
                return $status;
            }
        }

        return array_key_first($weights);
    }
}
