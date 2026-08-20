<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@sekolah.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Kepala Sekolah
        User::create([
            'name' => 'Kepala Sekolah',
            'email' => 'kepsek@sekolah.com',
            'password' => Hash::make('password123'),
            'role' => 'kepsek',
        ]);

        // Guru/Kurikulum
        User::create([
            'name' => 'Guru Matematika',
            'email' => 'guru@sekolah.com',
            'password' => Hash::make('password123'),
            'role' => 'guru',
        ]);

        // Siswa
        User::create([
            'name' => 'Ahmad Siswa',
            'email' => 'siswa@sekolah.com',
            'password' => Hash::make('password123'),
            'role' => 'siswa',
        ]);

        // Additional demo users
        User::create([
            'name' => 'Budi Guru',
            'email' => 'budi.guru@sekolah.com',
            'password' => Hash::make('password123'),
            'role' => 'kurikulum',
        ]);

        User::create([
            'name' => 'Siti Siswa',
            'email' => 'siti.siswa@sekolah.com',
            'password' => Hash::make('password123'),
            'role' => 'siswa',
        ]);

        $this->command->info('✅ User seeder completed successfully!');
        $this->command->info('Demo credentials:');
        $this->command->info('  Admin: admin@sekolah.com / password123');
        $this->command->info('  Kepsek: kepsek@sekolah.com / password123');
        $this->command->info('  Guru: guru@sekolah.com / password123');
        $this->command->info('  Siswa: siswa@sekolah.com / password123');
    }
}
