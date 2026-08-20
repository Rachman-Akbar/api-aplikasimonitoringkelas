<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Jadwal;
use Carbon\Carbon;

class TodayAndFutureDataSeeder extends Seeder
{
    /**
     * Run the database seeds to populate today and future data for specific requirements.
     */
    public function run(): void
    {
        $today = Carbon::today();
        $this->command->info("Adding records for today: {$today->toDateString()} and future dates");

        // Add 10 Kehadiran Siswa records for today (tanggal hari ini)
        $this->addKehadiranSiswa($today);

        // Add 10 Kehadiran Guru records for today (tanggal hari ini)
        $this->addKehadiranGuru($today);

        // Add 10 Guru Pengganti records for today and future dates (tanggal hari ini dan hari yang akan datang)
        $this->addGuruPengganti($today);

        // Add 10 Izin Guru records for today and future dates (tanggal hari ini dan hari yang akan datang)
        $this->addIzinGuru($today);

        $this->command->info("✅ Today and future data seeding completed successfully with 10 records each!");
    }

    private function addKehadiranSiswa($today)
    {
        $jadwals = \App\Models\Jadwal::all();
        $siswas = \App\Models\Siswa::all();

        if ($jadwals->count() > 0 && $siswas->count() > 0) {
            $this->command->info("Creating 10 Kehadiran Siswa records for today: {$today->toDateString()}");
            
            for ($i = 0; $i < 10; $i++) {
                // Rotate through schedules and students to create different records
                $jadwal = $jadwals[$i % $jadwals->count()];
                $siswa = $siswas[$i % $siswas->count()];

                $kehadiran = \App\Models\Kehadiran::create([
                    'siswa_id' => $siswa->id,
                    'jadwal_id' => $jadwal->id,
                    'tanggal' => $today,
                    'status' => $this->getRandomSiswaAttendanceStatus(),
                    'keterangan' => $this->getRandomKehadiranKeterangan(),
                    'created_at' => $today,
                    'updated_at' => $today,
                ]);

                $this->command->info("✅ Added Kehadiran Siswa record #".($i+1)." for {$today->toDateString()}");
            }
        } else {
            $this->command->warn("⚠️  Insufficient data for Kehadiran Siswa seeding");
        }
    }

    private function addKehadiranGuru($today)
    {
        $jadwals = \App\Models\Jadwal::all();
        $gurus = Guru::all();

        if ($jadwals->count() > 0 && $gurus->count() > 0) {
            $this->command->info("Creating 10 Kehadiran Guru records for today: {$today->toDateString()}");

            for ($i = 0; $i < 10; $i++) {
                // Rotate through schedules and teachers to create different records
                $jadwal = $jadwals[$i % $jadwals->count()];
                $guru = $gurus[$i % $gurus->count()];

                $kehadiranGuru = \App\Models\KehadiranGuru::create([
                    'jadwal_id' => $jadwal->id,
                    'guru_id' => $guru->id,
                    'tanggal' => $today,
                    'status_kehadiran' => $this->getRandomGuruAttendanceStatus(),
                    'waktu_datang' => $this->getRandomTime(),
                    'keterangan' => $this->getRandomKehadiranKeterangan(),
                    'created_at' => $today,
                    'updated_at' => $today,
                ]);

                $this->command->info("✅ Added Kehadiran Guru record #".($i+1)." for {$today->toDateString()}");
            }
        } else {
            $this->command->warn("⚠️  Insufficient data for Kehadiran Guru seeding");
        }
    }

    private function addGuruPengganti($today)
    {
        $jadwals = \App\Models\Jadwal::all();
        $gurus = Guru::all();

        if ($jadwals->count() > 0 && $gurus->count() > 1) {
            $this->command->info("Creating 10 Guru Pengganti records for today and future dates");
            
            for ($i = 0; $i < 10; $i++) {
                // Rotate through schedules and teachers to create different records
                $jadwal = $jadwals[$i % $jadwals->count()];
                
                // Get two different teachers for each record
                $guruAsli = $gurus[$i % $gurus->count()];
                $guruPenggantiIndex = ($i + 1) % $gurus->count();
                if ($guruPenggantiIndex == $i % $gurus->count()) {
                    // If there's only one teacher, use the same
                    $guruPengganti = $guruAsli;
                } else {
                    $guruPengganti = $gurus[$guruPenggantiIndex];
                }

                // Use today or future dates
                $tanggal = $today->copy()->addDays($i);

                $guruPenggantiRecord = \App\Models\GuruPengganti::create([
                    'jadwal_id' => $jadwal->id,
                    'tanggal' => $tanggal,
                    'guru_asli_id' => $guruAsli->id,
                    'guru_pengganti_id' => $guruPengganti->id,
                    'alasan_penggantian' => $this->getRandomPenggantiReason(),
                    'status_penggantian' => $this->getRandomPenggantiStatus(),
                    'keterangan' => 'Guru pengganti untuk menggantikan guru asli',
                    'created_at' => $tanggal,
                    'updated_at' => $tanggal,
                ]);

                $this->command->info("✅ Added Guru Pengganti record #".($i+1)." for {$tanggal->toDateString()}");
            }
        } else {
            $this->command->warn("⚠️  Insufficient data for Guru Pengganti seeding");
        }
    }

    private function addIzinGuru($today)
    {
        $gurus = Guru::all();
        if ($gurus->count() > 0) {
            $this->command->info("Creating 10 Izin Guru records for today and future dates");
            
            for ($i = 0; $i < 10; $i++) {
                // Rotate through teachers to create different records
                $guru = $gurus[$i % $gurus->count()];

                // Set tanggal_mulai as today or a future date
                $tanggalMulai = $today->copy()->addDays($i);
                // Set tanggal_selesai as 1 day after tanggal_mulai
                $tanggalSelesai = $tanggalMulai->copy()->addDays(1);

                $izinGuru = \App\Models\IzinGuru::create([
                    'guru_id' => $guru->id,
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_selesai' => $tanggalSelesai,
                    'durasi_hari' => 1,
                    'jenis_izin' => $this->getRandomIzinType(),
                    'keterangan' => $this->getRandomIzinKeterangan(),
                    'status_approval' => $this->getRandomApprovalStatus(),
                    'created_at' => $tanggalMulai,
                    'updated_at' => $tanggalMulai,
                ]);

                $this->command->info("✅ Added Izin Guru record #".($i+1)." for {$tanggalMulai->toDateString()}");
            }
        } else {
            $this->command->warn("⚠️  No teachers found for Izin Guru seeding");
        }
    }

    private function getRandomIzinType(): string
    {
        $types = ['sakit', 'izin', 'cuti', 'dinas_luar', 'lainnya'];
        return $types[array_rand($types)];
    }

    private function getRandomIzinKeterangan(): string
    {
        $keterangan = [
            'Sakit flu berat',
            'Acara keluarga penting',
            'Cuti tahunan',
            'Dinas luar kota',
            'Izin pribadi',
            'Sakit berobat',
            'Keperluan keluarga',
            'Sakit demam',
            'Sakit gigi',
            'Menghadiri undangan resmi'
        ];
        return $keterangan[array_rand($keterangan)];
    }

    private function getRandomApprovalStatus(): string
    {
        $statuses = ['pending', 'disetujui', 'ditolak'];
        return $statuses[array_rand($statuses)];
    }

    private function getRandomPenggantiReason(): string
    {
        $reasons = [
            'Sakit',
            'Dinas luar',
            'Izin acara keluarga',
            'Cuti tahunan',
            'Keperluan pribadi',
            'Sakit berobat',
            'Menghadiri pelatihan',
            'Acara resmi',
            'Dinas ke luar kota',
            'Keperluan keluarga'
        ];
        return $reasons[array_rand($reasons)];
    }

    private function getRandomPenggantiStatus(): string
    {
        $statuses = ['dijadwalkan', 'selesai', 'dibatalkan'];
        return $statuses[array_rand($statuses)];
    }

    private function getRandomGuruAttendanceStatus(): string
    {
        $statuses = ['hadir', 'telat', 'tidak_hadir', 'izin', 'sakit'];
        return $statuses[array_rand($statuses)];
    }

    private function getRandomSiswaAttendanceStatus(): string
    {
        $statuses = ['hadir', 'tidak_hadir', 'sakit', 'izin'];
        return $statuses[array_rand($statuses)];
    }

    private function getRandomTime(): string
    {
        $times = [
            '07:00', '07:15', '07:30', '07:45', '08:00', '08:15', '08:30', '08:45', 
            '09:00', '09:15', '09:30', '09:45', '10:00', '10:15', '10:30', '10:45',
            '11:00', '11:15', '11:30', '11:45', '12:00', '12:15', '12:30', '12:45'
        ];
        return $times[array_rand($times)];
    }

    private function getRandomKehadiranKeterangan(): string
    {
        $keterangan = [
            'Hadir tepat waktu',
            'Hadir lebih awal',
            'Hadir terlambat sedikit',
            'Tidak hadir tanpa izin',
            'Izin karena sakit',
            'Sakit tidak bisa hadir',
            'Hadir dengan sedikit keterlambatan',
            'Izin karena acara keluarga',
            'Sakit berobat',
            'Hadir dalam pelajaran',
            'Izin pribadi',
            'Tidak hadir karena alasan pribadi'
        ];
        return $keterangan[array_rand($keterangan)];
    }
}