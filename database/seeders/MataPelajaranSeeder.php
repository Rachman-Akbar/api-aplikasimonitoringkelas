<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MataPelajaran;

class MataPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📖 Creating 10 subjects...');

        $mataPelajaranData = [
            [
                'nama' => 'Matematika',
                'kode' => 'MTK',
                'deskripsi' => 'Mata pelajaran Matematika Umum',
                'sks' => 3,
                'kategori' => 'Normatif',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Bahasa Indonesia',
                'kode' => 'BIN',
                'deskripsi' => 'Mata pelajaran Bahasa Indonesia',
                'sks' => 3,
                'kategori' => 'Normatif',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Bahasa Inggris',
                'kode' => 'BIG',
                'deskripsi' => 'Mata pelajaran Bahasa Inggris',
                'sks' => 2,
                'kategori' => 'Normatif',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Pendidikan Agama dan Budi Pekerti',
                'kode' => 'PABP',
                'deskripsi' => 'Mata pelajaran Pendidikan Agama dan Budi Pekerti',
                'sks' => 2,
                'kategori' => 'Normatif',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Pendidikan Pancasila dan Kewarganegaraan',
                'kode' => 'PKN',
                'deskripsi' => 'Mata pelajaran Pendidikan Pancasila dan Kewarganegaraan',
                'sks' => 2,
                'kategori' => 'Normatif',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Pemrograman Berorientasi Objek',
                'kode' => 'PBO',
                'deskripsi' => 'Mata pelajaran Pemrograman Berorientasi Objek',
                'sks' => 4,
                'kategori' => 'Adaptif',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Basis Data',
                'kode' => 'DB',
                'deskripsi' => 'Mata pelajaran Basis Data',
                'sks' => 3,
                'kategori' => 'Adaptif',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Sistem Komputer',
                'kode' => 'SK',
                'deskripsi' => 'Mata pelajaran Sistem Komputer',
                'sks' => 2,
                'kategori' => 'Adaptif',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Pemrograman Web',
                'kode' => 'WEB',
                'deskripsi' => 'Mata pelajaran Pemrograman Web',
                'sks' => 4,
                'kategori' => 'Keahlian',
                'status' => 'aktif'
            ],
            [
                'nama' => 'Pemrograman Mobile',
                'kode' => 'MOB',
                'deskripsi' => 'Mata pelajaran Pemrograman Mobile',
                'sks' => 4,
                'kategori' => 'Keahlian',
                'status' => 'aktif'
            ]
        ];

        foreach ($mataPelajaranData as $mataPelajaran) {
            MataPelajaran::create($mataPelajaran);
        }

        $this->command->info('✅ Successfully created 10 subjects');
    }
}