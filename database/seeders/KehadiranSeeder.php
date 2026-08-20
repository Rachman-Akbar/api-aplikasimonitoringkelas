<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kehadiran;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Models\User;

class KehadiranSeeder extends Seeder
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

        // Get existing siswas from these specific classes and jadwals for foreign key references
        $siswas = Siswa::whereIn('kelas_id', [$kelasRPL->id, $kelasTKJ->id])->pluck('id')->toArray();
        $jadwals = Jadwal::pluck('id')->toArray();

        // If no students found in these classes, we'll create some sample records
        if (empty($siswas)) {
            $this->command->info('ℹ️ No students found in XII RPL 1 or XII TKJ 1. Creating test students...');

            // Create sample students for these classes
            $siswaRPL = Siswa::create([
                'nis' => '123456789011',
                'nisn' => '011123456789',
                'nama' => 'Budi Rahman',
                'email' => 'budi.rahman@sekolah.com',
                'no_telp' => '081312345611',
                'alamat' => 'Jl. RPL No. 1 Jakarta',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => \Carbon\Carbon::create(2005, 3, 15),
                'kelas_id' => $kelasRPL->id,
                'nama_orang_tua' => 'Bapak Rahman',
                'no_telp_orang_tua' => '081312345011',
                'status' => 'aktif'
            ]);

            $siswaTKJ = Siswa::create([
                'nis' => '123456789012',
                'nisn' => '012123456789',
                'nama' => 'Ani Kusuma',
                'email' => 'ani.kusuma@sekolah.com',
                'no_telp' => '081312345612',
                'alamat' => 'Jl. TKJ No. 1 Jakarta',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => \Carbon\Carbon::create(2005, 4, 20),
                'kelas_id' => $kelasTKJ->id,
                'nama_orang_tua' => 'Ibu Kusuma',
                'no_telp_orang_tua' => '081312345012',
                'status' => 'aktif'
            ]);

            $siswas = [$siswaRPL->id, $siswaTKJ->id];
        }

        $kehadiranData = [
            [
                'siswa_id' => $siswas[array_rand($siswas)],
                'jadwal_id' => $jadwals[array_rand($jadwals)],
                'tanggal' => now()->subDays(5)->toDateString(),
                'status' => 'hadir',
                'keterangan' => 'Hadir',
            ],
            [
                'siswa_id' => $siswas[array_rand($siswas)],
                'jadwal_id' => $jadwals[array_rand($jadwals)],
                'tanggal' => now()->subDays(4)->toDateString(),
                'status' => 'izin',
                'keterangan' => 'Izin karena acara keluarga',
            ],
            [
                'siswa_id' => $siswas[array_rand($siswas)],
                'jadwal_id' => $jadwals[array_rand($jadwals)],
                'tanggal' => now()->subDays(3)->toDateString(),
                'status' => 'sakit',
                'keterangan' => 'Sakit pilek',
            ],
            [
                'siswa_id' => $siswas[array_rand($siswas)],
                'jadwal_id' => $jadwals[array_rand($jadwals)],
                'tanggal' => now()->subDays(2)->toDateString(),
                'status' => 'tidak_hadir',
                'keterangan' => 'Tanpa keterangan',
            ],
            [
                'siswa_id' => $siswas[array_rand($siswas)],
                'jadwal_id' => $jadwals[array_rand($jadwals)],
                'tanggal' => now()->subDays(1)->toDateString(),
                'status' => 'hadir',
                'keterangan' => 'Hadir',
            ],
            [
                'siswa_id' => $siswas[array_rand($siswas)],
                'jadwal_id' => $jadwals[array_rand($jadwals)],
                'tanggal' => now()->subDays(6)->toDateString(),
                'status' => 'tidak_hadir',
                'keterangan' => 'Tidak hadir tanpa izin',
            ],
            [
                'siswa_id' => $siswas[array_rand($siswas)],
                'jadwal_id' => $jadwals[array_rand($jadwals)],
                'tanggal' => now()->subDays(7)->toDateString(),
                'status' => 'hadir',
                'keterangan' => 'Hadir',
            ],
            [
                'siswa_id' => $siswas[array_rand($siswas)],
                'jadwal_id' => $jadwals[array_rand($jadwals)],
                'tanggal' => now()->subDays(8)->toDateString(),
                'status' => 'izin',
                'keterangan' => 'Izin karena sakit',
            ],
            [
                'siswa_id' => $siswas[array_rand($siswas)],
                'jadwal_id' => $jadwals[array_rand($jadwals)],
                'tanggal' => now()->subDays(9)->toDateString(),
                'status' => 'sakit',
                'keterangan' => 'Sakit demam',
            ],
            [
                'siswa_id' => $siswas[array_rand($siswas)],
                'jadwal_id' => $jadwals[array_rand($jadwals)],
                'tanggal' => now()->subDays(10)->toDateString(),
                'status' => 'hadir',
                'keterangan' => 'Hadir',
            ],
        ];

        foreach ($kehadiranData as $data) {
            Kehadiran::create($data);
        }

        $this->command->info('✅ ' . count($kehadiranData) . ' Kehadiran Siswa records for XII RPL 1 and XII TKJ 1 seeded successfully!');
    }
}