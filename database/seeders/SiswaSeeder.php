<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use Carbon\Carbon;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎓 Creating 10 students...');

        $kelasIds = Kelas::pluck('id')->toArray();
        if (empty($kelasIds)) {
            $this->command->error('❌ No classes found. Please run KelasSeeder first.');
            return;
        }

        $siswaUsers = User::where('role', 'siswa')->get()->take(10);

        $siswaData = [
            [
                'nis' => '123456789001',
                'nisn' => '001123456789',
                'nama' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@sekolah.com',
                'no_telp' => '081312345601',
                'alamat' => 'Jl. Merdeka No. 1 Jakarta Pusat',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => Carbon::create(2005, 5, 15),
                'kelas_id' => $kelasIds[0], // Assign to first class
                'nama_orang_tua' => 'Bapak Fauzi',
                'no_telp_orang_tua' => '081312345001',
                'status' => 'aktif'
            ],
            [
                'nis' => '123456789002',
                'nisn' => '002123456789',
                'nama' => 'Siti Aisyah',
                'email' => 'siti.aisyah@sekolah.com',
                'no_telp' => '081312345602',
                'alamat' => 'Jl. Sudirman No. 5 Jakarta Pusat',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => Carbon::create(2005, 8, 22),
                'kelas_id' => $kelasIds[0], // Assign to first class
                'nama_orang_tua' => 'Ibu Aisyah',
                'no_telp_orang_tua' => '081312345002',
                'status' => 'aktif'
            ],
            [
                'nis' => '123456789003',
                'nisn' => '003123456789',
                'nama' => 'Budi Prasetyo',
                'email' => 'budi.prasetyo@sekolah.com',
                'no_telp' => '081312345603',
                'alamat' => 'Jl. Thamrin No. 10 Jakarta Selatan',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => Carbon::create(2005, 3, 10),
                'kelas_id' => $kelasIds[1], // Assign to second class
                'nama_orang_tua' => 'Bapak Prasetyo',
                'no_telp_orang_tua' => '081312345003',
                'status' => 'aktif'
            ],
            [
                'nis' => '123456789004',
                'nisn' => '004123456789',
                'nama' => 'Dewi Kartika',
                'email' => 'dewi.kartika@sekolah.com',
                'no_telp' => '081312345604',
                'alamat' => 'Jl. Gatot Subroto No. 15 Jakarta Selatan',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => Carbon::create(2005, 11, 7),
                'kelas_id' => $kelasIds[1], // Assign to second class
                'nama_orang_tua' => 'Ibu Kartika',
                'no_telp_orang_tua' => '081312345004',
                'status' => 'aktif'
            ],
            [
                'nis' => '123456789005',
                'nisn' => '005123456789',
                'nama' => 'Eko Prasetyo',
                'email' => 'eko.prasetyo@sekolah.com',
                'no_telp' => '081312345605',
                'alamat' => 'Jl. HR Rasuna Said No. 20 Jakarta Selatan',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => Carbon::create(2005, 1, 30),
                'kelas_id' => $kelasIds[2], // Assign to third class
                'nama_orang_tua' => 'Bapak Prasetyo',
                'no_telp_orang_tua' => '081312345005',
                'status' => 'aktif'
            ],
            [
                'nis' => '123456789006',
                'nisn' => '006123456789',
                'nama' => 'Fitri Lestari',
                'email' => 'fitri.lestari@sekolah.com',
                'no_telp' => '081312345606',
                'alamat' => 'Jl. Asia Afrika No. 25 Bandung',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => Carbon::create(2005, 6, 18),
                'kelas_id' => $kelasIds[2], // Assign to third class
                'nama_orang_tua' => 'Ibu Lestari',
                'no_telp_orang_tua' => '081312345006',
                'status' => 'aktif'
            ],
            [
                'nis' => '123456789007',
                'nisn' => '007123456789',
                'nama' => 'Ganteng Pratama',
                'email' => 'ganteng.pratama@sekolah.com',
                'no_telp' => '081312345607',
                'alamat' => 'Jl. Diponegoro No. 30 Bandung',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => Carbon::create(2005, 9, 5),
                'kelas_id' => $kelasIds[3], // Assign to fourth class
                'nama_orang_tua' => 'Bapak Pratama',
                'no_telp_orang_tua' => '081312345007',
                'status' => 'aktif'
            ],
            [
                'nis' => '123456789008',
                'nisn' => '008123456789',
                'nama' => 'Heni Marlina',
                'email' => 'heni.marlina@sekolah.com',
                'no_telp' => '081312345608',
                'alamat' => 'Jl. Veteran No. 35 Surabaya',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => Carbon::create(2005, 12, 12),
                'kelas_id' => $kelasIds[3], // Assign to fourth class
                'nama_orang_tua' => 'Ibu Marlina',
                'no_telp_orang_tua' => '081312345008',
                'status' => 'aktif'
            ],
            [
                'nis' => '123456789009',
                'nisn' => '009123456789',
                'nama' => 'Indra Kusuma',
                'email' => 'indra.kusuma@sekolah.com',
                'no_telp' => '081312345609',
                'alamat' => 'Jl. Gubernur Suryadarma No. 40 Surabaya',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => Carbon::create(2005, 4, 25),
                'kelas_id' => $kelasIds[4], // Assign to fifth class
                'nama_orang_tua' => 'Bapak Kusuma',
                'no_telp_orang_tua' => '081312345009',
                'status' => 'aktif'
            ],
            [
                'nis' => '123456789010',
                'nisn' => '010123456789',
                'nama' => 'Juli Ratnasari',
                'email' => 'juli.ratnasari@sekolah.com',
                'no_telp' => '081312345610',
                'alamat' => 'Jl. Ahmad Yani No. 45 Medan',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => Carbon::create(2005, 7, 8),
                'kelas_id' => $kelasIds[4], // Assign to fifth class
                'nama_orang_tua' => 'Ibu Ratnasari',
                'no_telp_orang_tua' => '081312345010',
                'status' => 'aktif'
            ]
        ];

        foreach ($siswaData as $index => $siswa) {
            $siswaModel = Siswa::create($siswa);
            
            // Link to user if available
            if (isset($siswaUsers[$index])) {
                $siswaUsers[$index]->update(['kelas_id' => $siswa['kelas_id']]);
                $siswaModel->update(['user_id' => $siswaUsers[$index]->id]);
            }
        }

        // Update total student count in classes
        foreach ($kelasIds as $kelasId) {
            $jumlahSiswa = Siswa::where('kelas_id', $kelasId)->count();
            Kelas::where('id', $kelasId)->update(['jumlah_siswa' => $jumlahSiswa]);
        }

        $this->command->info('✅ Successfully created 10 students');
    }
}