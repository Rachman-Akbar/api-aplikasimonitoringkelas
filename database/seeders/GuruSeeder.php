<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guru;
use App\Models\User;
use Carbon\Carbon;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👨‍🏫 Creating 10 teachers...');

        $guruUsers = User::where('role', 'guru')->get()->take(10);
        $guruEmails = ['siti@sekolah.com', 'budi@sekolah.com', 'lina@sekolah.com', 'agus@sekolah.com', 'dian@sekolah.com'];

        $guruData = [
            [
                'nip' => '198001012010011001',
                'nama' => 'Bu Siti Rahayu',
                'email' => 'siti@sekolah.com',
                'no_telp' => '081234567890',
                'alamat' => 'Jl. Merdeka No. 1 Jakarta Pusat',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => Carbon::create(1980, 1, 1),
                'status' => 'aktif'
            ],
            [
                'nip' => '197502022005022002',
                'nama' => 'Pak Budi Santoso',
                'email' => 'budi@sekolah.com',
                'no_telp' => '081234567891',
                'alamat' => 'Jl. Sudirman No. 5 Jakarta Pusat',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => Carbon::create(1975, 2, 2),
                'status' => 'aktif'
            ],
            [
                'nip' => '198203032012033003',
                'nama' => 'Bu Lina Marlina',
                'email' => 'lina@sekolah.com',
                'no_telp' => '081234567892',
                'alamat' => 'Jl. Thamrin No. 10 Jakarta Pusat',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => Carbon::create(1982, 3, 3),
                'status' => 'aktif'
            ],
            [
                'nip' => '197804042008044004',
                'nama' => 'Pak Agus Prasetyo',
                'email' => 'agus@sekolah.com',
                'no_telp' => '081234567893',
                'alamat' => 'Jl. Gatot Subroto No. 15 Jakarta Selatan',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => Carbon::create(1978, 4, 4),
                'status' => 'aktif'
            ],
            [
                'nip' => '198505052015055005',
                'nama' => 'Bu Dian Lestari',
                'email' => 'dian@sekolah.com',
                'no_telp' => '081234567894',
                'alamat' => 'Jl. HR Rasuna Said No. 20 Jakarta Selatan',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => Carbon::create(1985, 5, 5),
                'status' => 'aktif'
            ],
            [
                'nip' => '197606062006066006',
                'nama' => 'Pak Joko Widodo',
                'email' => 'joko@sekolah.com',
                'no_telp' => '081234567895',
                'alamat' => 'Jl. Asia Afrika No. 25 Bandung',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => Carbon::create(1976, 6, 6),
                'status' => 'aktif'
            ],
            [
                'nip' => '198307072013077007',
                'nama' => 'Bu Ratna Sari',
                'email' => 'ratna@sekolah.com',
                'no_telp' => '081234567896',
                'alamat' => 'Jl. Diponegoro No. 30 Bandung',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => Carbon::create(1983, 7, 7),
                'status' => 'aktif'
            ],
            [
                'nip' => '197908082009088008',
                'nama' => 'Pak Fajar Satria',
                'email' => 'fajar@sekolah.com',
                'no_telp' => '081234567897',
                'alamat' => 'Jl. Veteran No. 35 Surabaya',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => Carbon::create(1979, 8, 8),
                'status' => 'aktif'
            ],
            [
                'nip' => '198409092014099009',
                'nama' => 'Bu Lili Marliana',
                'email' => 'lili@sekolah.com',
                'no_telp' => '081234567898',
                'alamat' => 'Jl. Gubernur Suryadarma No. 40 Surabaya',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => Carbon::create(1984, 9, 9),
                'status' => 'aktif'
            ],
            [
                'nip' => '197710102007101010',
                'nama' => 'Pak Adi Prasetyo',
                'email' => 'adi@sekolah.com',
                'no_telp' => '081234567899',
                'alamat' => 'Jl. Ahmad Yani No. 45 Medan',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => Carbon::create(1977, 10, 10),
                'status' => 'aktif'
            ]
        ];

        foreach ($guruData as $index => $guru) {
            $guruModel = Guru::create($guru);
            
            // Link to user if available
            if (isset($guruUsers[$index])) {
                $guruUsers[$index]->update(['guru_id' => $guruModel->id]);
                $guruModel->update(['user_id' => $guruUsers[$index]->id]);
            }
        }

        $this->command->info('✅ Successfully created 10 teachers');
    }
}