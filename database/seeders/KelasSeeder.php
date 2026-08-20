<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📚 Creating 5 classes...');

        $kelasData = [
            [
                'nama' => 'XII RPL 1',
                'tingkat' => 12,
                'jurusan' => 'Rekayasa Perangkat Lunak',
                'kapasitas' => 32,
                'jumlah_siswa' => 0,
                'ruangan' => 'Ruang 101',
                'status' => 'aktif'
            ],
            [
                'nama' => 'XII RPL 2',
                'tingkat' => 12,
                'jurusan' => 'Rekayasa Perangkat Lunak',
                'kapasitas' => 30,
                'jumlah_siswa' => 0,
                'ruangan' => 'Ruang 102',
                'status' => 'aktif'
            ],
            [
                'nama' => 'XII TKJ 1',
                'tingkat' => 12,
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'kapasitas' => 28,
                'jumlah_siswa' => 0,
                'ruangan' => 'Ruang 201',
                'status' => 'aktif'
            ],
            [
                'nama' => 'XI RPL 1',
                'tingkat' => 11,
                'jurusan' => 'Rekayasa Perangkat Lunak',
                'kapasitas' => 32,
                'jumlah_siswa' => 0,
                'ruangan' => 'Ruang 301',
                'status' => 'aktif'
            ],
            [
                'nama' => 'X RPL 1',
                'tingkat' => 10,
                'jurusan' => 'Rekayasa Perangkat Lunak',
                'kapasitas' => 35,
                'jumlah_siswa' => 0,
                'ruangan' => 'Ruang 401',
                'status' => 'aktif'
            ]
        ];

        foreach ($kelasData as $kelas) {
            Kelas::create($kelas);
        }

        $this->command->info('✅ Successfully created 5 classes');
    }
}