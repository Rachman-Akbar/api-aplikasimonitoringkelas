<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\GuruPengganti;
use App\Models\IzinGuru;
use App\Models\Jadwal;
use App\Models\Kehadiran;
use App\Models\KehadiranGuru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class FiftyDataSeeder extends Seeder
{
    private const TOTAL = 50;

    public function run(): void
    {
        $this->assertRequiredTablesExist();

        DB::transaction(function (): void {
            $gurus = $this->seedGurus();
            $kelas = $this->seedKelas($gurus);
            $mataPelajarans = $this->seedMataPelajarans();
            $siswas = $this->seedSiswas($kelas);
            $users = $this->seedUsers($gurus, $kelas, $siswas);
            $this->linkUsers($users, $gurus, $siswas);
            $jadwals = $this->seedJadwals($kelas, $mataPelajarans, $gurus);
            $this->seedKehadirans($siswas, $jadwals, $users);
            $this->seedKehadiranGurus($jadwals, $gurus, $users);
            $this->seedIzinGurus($gurus, $users);
            $this->seedGuruPengganties($jadwals, $gurus, $users);
            $this->syncJumlahSiswa($kelas);
            $this->resetAllUserPasswords();
        });

        $this->command?->info('Seeder 50 data selesai. Semua akun user menggunakan password login: 123');
    }

    private function assertRequiredTablesExist(): void
    {
        $tables = [
            'users',
            'gurus',
            'siswas',
            'kelas',
            'mata_pelajarans',
            'jadwals',
            'kehadirans',
            'kehadiran_gurus',
            'izin_gurus',
            'guru_pengganties',
        ];

        $missing = array_values(array_filter($tables, fn (string $table): bool => !Schema::hasTable($table)));

        if ($missing !== []) {
            throw new RuntimeException('Tabel belum tersedia: '.implode(', ', $missing).'. Seeder tidak menjalankan migration.');
        }
    }

    private function seedGurus(): array
    {
        $result = [];

        for ($i = 1; $i <= self::TOTAL; $i++) {
            $number = str_pad((string) $i, 3, '0', STR_PAD_LEFT);
            $nip = '198600'.$number;
            $guru = Guru::withTrashed()->where('nip', $nip)->first() ?? new Guru();
            $guru->fill([
                'nip' => $nip,
                'nama' => 'Guru Demo '.$number,
                'email' => 'guru'.$number.'@monitoringkelas.test',
                'no_telp' => '0812000'.str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'alamat' => 'Alamat Guru Demo '.$number,
                'jenis_kelamin' => $i % 2 === 0 ? 'P' : 'L',
                'tanggal_lahir' => CarbonImmutable::create(1980 + ($i % 15), (($i - 1) % 12) + 1, (($i - 1) % 27) + 1)->toDateString(),
                'status' => 'aktif',
            ]);
            $guru->deleted_at = null;
            $guru->save();
            $result[] = $guru;
        }

        return $result;
    }

    private function seedKelas(array $gurus): array
    {
        $jurusans = [
            'rekayasa perangkat lunak',
            'teknik komputer jaringan',
            'akuntansi',
            'manajemen perkantoran',
            'desain komunikasi visual',
        ];
        $result = [];

        for ($i = 1; $i <= self::TOTAL; $i++) {
            $number = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $tingkat = 10 + (($i - 1) % 3);
            $nama = 'kelas '.$tingkat.' demo '.$number;
            $kelas = Kelas::withTrashed()->where('nama', $nama)->first() ?? new Kelas();
            $kelas->fill([
                'nama' => $nama,
                'tingkat' => $tingkat,
                'jurusan' => $jurusans[($i - 1) % count($jurusans)],
                'wali_kelas_id' => $gurus[$i - 1]->id,
                'kapasitas' => 36,
                'jumlah_siswa' => 0,
                'ruangan' => 'ruang '.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'status' => 'aktif',
            ]);
            $kelas->deleted_at = null;
            $kelas->save();
            $result[] = $kelas;
        }

        return $result;
    }

    private function seedMataPelajarans(): array
    {
        $kategori = ['normatif', 'adaptif', 'kejuruan', 'keahlian'];
        $result = [];

        for ($i = 1; $i <= self::TOTAL; $i++) {
            $number = str_pad((string) $i, 3, '0', STR_PAD_LEFT);
            $kode = 'mp'.$number;
            $nama = 'mata pelajaran demo '.$number;
            $mapel = MataPelajaran::withTrashed()->where('kode', $kode)->first() ?? new MataPelajaran();
            $mapel->fill([
                'kode' => $kode,
                'nama' => $nama,
                'deskripsi' => 'Mata pelajaran data demo '.$number,
                'sks' => (($i - 1) % 4) + 1,
                'kategori' => $kategori[($i - 1) % count($kategori)],
                'status' => 'aktif',
            ]);
            $mapel->deleted_at = null;
            $mapel->save();
            $result[] = $mapel;
        }

        return $result;
    }

    private function seedSiswas(array $kelas): array
    {
        $result = [];

        for ($i = 1; $i <= self::TOTAL; $i++) {
            $number = str_pad((string) $i, 3, '0', STR_PAD_LEFT);
            $nis = '260'.$number;
            $nisn = '006260'.$number;
            $siswa = Siswa::withTrashed()->where('nis', $nis)->first() ?? new Siswa();
            $siswa->fill([
                'nis' => $nis,
                'nisn' => $nisn,
                'nama' => 'Siswa Demo '.$number,
                'email' => 'siswa'.$number.'@monitoringkelas.test',
                'no_telp' => '0821000'.str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'alamat' => 'Alamat Siswa Demo '.$number,
                'jenis_kelamin' => $i % 2 === 0 ? 'P' : 'L',
                'tanggal_lahir' => CarbonImmutable::create(2008 + ($i % 3), (($i - 1) % 12) + 1, (($i - 1) % 27) + 1)->toDateString(),
                'kelas_id' => $kelas[$i - 1]->id,
                'nama_orang_tua' => 'Orang Tua Siswa '.$number,
                'no_telp_orang_tua' => '0838000'.str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'status' => 'aktif',
            ]);
            $siswa->deleted_at = null;
            $siswa->save();
            $result[] = $siswa;
        }

        return $result;
    }

    private function seedUsers(array $gurus, array $kelas, array $siswas): array
    {
        $result = [];

        for ($i = 1; $i <= self::TOTAL; $i++) {
            $number = str_pad((string) $i, 3, '0', STR_PAD_LEFT);
            [$role, $name, $email, $guruId, $kelasId] = $this->userDefinition($i, $number, $gurus, $kelas, $siswas);
            $user = User::where('email', $email)->first() ?? new User();
            $user->fill([
                'name' => $name,
                'email' => $email,
                'password' => '123',
                'role' => $role,
                'guru_id' => $guruId,
                'kelas_id' => $kelasId,
            ]);
            $user->email_verified_at = now();
            $user->save();
            $result[] = $user;
        }

        return $result;
    }

    private function userDefinition(int $i, string $number, array $gurus, array $kelas, array $siswas): array
    {
        if ($i === 1) {
            return ['admin', 'Administrator Demo', 'admin@monitoringkelas.test', null, null];
        }

        if ($i === 2) {
            return ['kepsek', 'Kepala Sekolah Demo', 'kepsek@monitoringkelas.test', null, null];
        }

        if ($i >= 3 && $i <= 5) {
            return ['kurikulum', 'Kurikulum Demo '.$number, 'kurikulum'.$number.'@monitoringkelas.test', null, null];
        }

        if ($i >= 6 && $i <= 25) {
            $guru = $gurus[$i - 6];

            return ['guru', $guru->nama, $guru->email, $guru->id, null];
        }

        $siswa = $siswas[$i - 26];

        return ['siswa', $siswa->nama, $siswa->email, null, $kelas[$i - 26]->id];
    }

    private function linkUsers(array $users, array $gurus, array $siswas): void
    {
        for ($i = 6; $i <= 25; $i++) {
            $gurus[$i - 6]->update(['user_id' => $users[$i - 1]->id]);
        }

        for ($i = 26; $i <= 50; $i++) {
            $siswas[$i - 26]->update(['user_id' => $users[$i - 1]->id]);
        }
    }

    private function seedJadwals(array $kelas, array $mataPelajarans, array $gurus): array
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $slots = [
            ['07:00', '07:45'],
            ['07:45', '08:30'],
            ['08:30', '09:15'],
            ['09:15', '10:00'],
            ['10:15', '11:00'],
            ['11:00', '11:45'],
            ['12:30', '13:15'],
            ['13:15', '14:00'],
            ['14:00', '14:45'],
            ['14:45', '15:30'],
        ];
        $result = [];

        for ($i = 1; $i <= self::TOTAL; $i++) {
            $slotIndex = ($i - 1) % count($slots);
            $jadwal = Jadwal::updateOrCreate(
                [
                    'kelas_id' => $kelas[$i - 1]->id,
                    'mata_pelajaran_id' => $mataPelajarans[$i - 1]->id,
                    'guru_id' => $gurus[$i - 1]->id,
                    'tahun_ajaran' => '2026/2027',
                ],
                [
                    'hari' => $days[($i - 1) % count($days)],
                    'jam_ke' => $slotIndex + 1,
                    'jam_mulai' => $slots[$slotIndex][0],
                    'jam_selesai' => $slots[$slotIndex][1],
                    'ruangan' => 'ruang '.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                    'status' => 'aktif',
                    'keterangan' => 'jadwal demo '.$i,
                ]
            );
            $result[] = $jadwal;
        }

        return $result;
    }

    private function seedKehadirans(array $siswas, array $jadwals, array $users): void
    {
        $statuses = ['hadir', 'hadir', 'hadir', 'izin', 'sakit', 'tidak_hadir'];
        $baseDate = CarbonImmutable::create(2026, 8, 3);
        $inputUserId = $users[0]->id;

        for ($i = 1; $i <= self::TOTAL; $i++) {
            $status = $statuses[($i - 1) % count($statuses)];
            Kehadiran::updateOrCreate(
                [
                    'siswa_id' => $siswas[$i - 1]->id,
                    'jadwal_id' => $jadwals[$i - 1]->id,
                    'tanggal' => $baseDate->addDays(($i - 1) % 18)->toDateString(),
                ],
                [
                    'status' => $status,
                    'keterangan' => $status === 'hadir' ? null : 'data kehadiran demo '.$i,
                    'diinput_oleh' => $inputUserId,
                ]
            );
        }
    }

    private function seedKehadiranGurus(array $jadwals, array $gurus, array $users): void
    {
        $statuses = ['hadir', 'hadir', 'telat', 'izin', 'sakit', 'tidak_hadir'];
        $baseDate = CarbonImmutable::create(2026, 8, 3);
        $inputUserId = $users[0]->id;

        for ($i = 1; $i <= self::TOTAL; $i++) {
            $status = $statuses[($i - 1) % count($statuses)];
            KehadiranGuru::updateOrCreate(
                [
                    'jadwal_id' => $jadwals[$i - 1]->id,
                    'guru_id' => $gurus[$i - 1]->id,
                    'tanggal' => $baseDate->addDays(($i - 1) % 18)->toDateString(),
                ],
                [
                    'status_kehadiran' => $status,
                    'waktu_datang' => in_array($status, ['hadir', 'telat'], true) ? ($status === 'telat' ? '07:10' : '07:00') : null,
                    'durasi_keterlambatan' => $status === 'telat' ? 10 : 0,
                    'keterangan' => $status === 'hadir' ? null : 'data kehadiran guru demo '.$i,
                    'diinput_oleh' => $inputUserId,
                ]
            );
        }
    }

    private function seedIzinGurus(array $gurus, array $users): void
    {
        $types = ['sakit', 'izin', 'cuti', 'dinas_luar', 'lainnya'];
        $statuses = ['pending', 'disetujui', 'ditolak'];
        $baseDate = CarbonImmutable::create(2026, 8, 1);
        $approverId = $users[2]->id;

        for ($i = 1; $i <= self::TOTAL; $i++) {
            $tanggalMulai = $baseDate->addDays($i - 1);
            $durasi = (($i - 1) % 3) + 1;
            $status = $statuses[($i - 1) % count($statuses)];
            IzinGuru::updateOrCreate(
                [
                    'guru_id' => $gurus[$i - 1]->id,
                    'tanggal_mulai' => $tanggalMulai->toDateString(),
                ],
                [
                    'tanggal_selesai' => $tanggalMulai->addDays($durasi - 1)->toDateString(),
                    'durasi_hari' => $durasi,
                    'jenis_izin' => $types[($i - 1) % count($types)],
                    'keterangan' => 'izin guru demo '.$i,
                    'status_approval' => $status,
                    'disetujui_oleh' => $status === 'pending' ? null : $approverId,
                    'tanggal_approval' => $status === 'pending' ? null : $tanggalMulai->subDay(),
                    'catatan_approval' => $status === 'pending' ? null : 'diproses untuk data demo',
                ]
            );
        }
    }

    private function seedGuruPengganties(array $jadwals, array $gurus, array $users): void
    {
        $statuses = ['pending', 'dijadwalkan', 'selesai', 'tidak_hadir'];
        $baseDate = CarbonImmutable::create(2026, 8, 3);
        $approverId = $users[2]->id;

        for ($i = 1; $i <= self::TOTAL; $i++) {
            $guruAsli = $gurus[$i - 1];
            $guruPengganti = $gurus[$i % self::TOTAL];
            $status = $statuses[($i - 1) % count($statuses)];
            GuruPengganti::updateOrCreate(
                [
                    'jadwal_id' => $jadwals[$i - 1]->id,
                    'tanggal' => $baseDate->addDays($i - 1)->toDateString(),
                ],
                [
                    'guru_asli_id' => $guruAsli->id,
                    'guru_pengganti_id' => $guruPengganti->id,
                    'status_penggantian' => $status,
                    'keterangan' => 'guru pengganti demo '.$i,
                    'catatan_approval' => $status === 'pending' ? null : 'disetujui untuk data demo',
                    'disetujui_oleh' => $status === 'pending' ? null : $approverId,
                ]
            );
        }
    }

    private function syncJumlahSiswa(array $kelas): void
    {
        foreach ($kelas as $item) {
            $item->update([
                'jumlah_siswa' => Siswa::where('kelas_id', $item->id)->whereNull('deleted_at')->count(),
            ]);
        }
    }

    private function resetAllUserPasswords(): void
    {
        User::query()->orderBy('id')->chunkById(100, function ($users): void {
            foreach ($users as $user) {
                $user->forceFill(['password' => '123'])->save();
            }
        });
    }
}
