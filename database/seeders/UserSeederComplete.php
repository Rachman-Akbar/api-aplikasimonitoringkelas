<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeederComplete extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📝 Creating 10 users with different roles...');

        // Define users with different roles
        $users = [
            // Admin
            [
                'name' => 'Administrator Sekolah',
                'email' => 'admin@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ],
            // Kepala Sekolah
            [
                'name' => 'Kepala Sekolah',
                'email' => 'kepsek@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'kepsek',
            ],
            // Kurikulum
            [
                'name' => 'Staf Kurikulum',
                'email' => 'kurikulum@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'kurikulum',
            ],
            // Guru 1
            [
                'name' => 'Bu Siti Rahayu',
                'email' => 'siti@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'guru',
            ],
            // Guru 2
            [
                'name' => 'Pak Budi Santoso',
                'email' => 'budi@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'guru',
            ],
            // Guru 3
            [
                'name' => 'Bu Lina Marlina',
                'email' => 'lina@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'guru',
            ],
            // Guru 4
            [
                'name' => 'Pak Agus Prasetyo',
                'email' => 'agus@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'guru',
            ],
            // Guru 5
            [
                'name' => 'Bu Dian Lestari',
                'email' => 'dian@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'guru',
            ],
            // Siswa 1
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad.siswa@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
            ],
            // Siswa 2
            [
                'name' => 'Siti Aisyah',
                'email' => 'siti.siswa@sekolah.com',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        $this->command->info('✅ Successfully created 10 users with different roles');
    }
}