<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;

class BasicTestDataSeeder extends Seeder
{
    public function run()
    {
        // Create some basic data for testing
        $kelas = Kelas::first();
        if (!$kelas) {
            $kelas = Kelas::create([
                'nama' => 'XII RPL 1',
                'tingkat' => 12,
                'jurusan' => 'Rekayasa Perangkat Lunak',
                'kapasitas' => 32,
                'jumlah_siswa' => 0,
                'ruangan' => 'Ruang 101',
                'status' => 'aktif'
            ]);
            $this->command->info('Kelas created for testing');
        }

        $guru1 = Guru::first();
        if (!$guru1) {
            $guru1 = Guru::create([
                'nama' => 'Pak Budi Santoso',
                'nip' => '1234567890',
                'email' => 'budi@example.com',
                'no_telp' => '081234567890',
                'alamat' => 'Jl. Merdeka No. 1',
                'jenis_kelamin' => 'L',
                'status' => 'aktif'
            ]);
            $this->command->info('Guru 1 created for testing');
        }

        $guru2 = Guru::skip(1)->first();
        if (!$guru2 && Guru::count() >= 1) {
            $guru2 = Guru::create([
                'nama' => 'Bu Siti Rahayu',
                'nip' => '0987654321',
                'email' => 'siti@example.com',
                'no_telp' => '081234567891',
                'alamat' => 'Jl. Sudirman No. 2',
                'jenis_kelamin' => 'P',
                'status' => 'aktif'
            ]);
            $this->command->info('Guru 2 created for testing');
        } elseif (!$guru2) {
            $guru2 = Guru::create([
                'nama' => 'Bu Siti Rahayu',
                'nip' => '0987654321',
                'email' => 'siti@example.com',
                'no_telp' => '081234567891',
                'alamat' => 'Jl. Sudirman No. 2',
                'jenis_kelamin' => 'P',
                'status' => 'aktif'
            ]);
            $this->command->info('Guru 2 created for testing');
        }

        $mapel = MataPelajaran::first();
        if (!$mapel) {
            $mapel = MataPelajaran::create([
                'nama' => 'Matematika',
                'kode' => 'MTK',
                'deskripsi' => 'Mata pelajaran Matematika',
                'sks' => 3,
                'kategori' => 'Umum',
                'status' => 'aktif'
            ]);
            $this->command->info('Mata Pelajaran created for testing');
        }

        $this->command->info('Basic test data created successfully!');
    }
}