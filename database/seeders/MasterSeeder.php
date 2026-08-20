<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            UserSeederComplete::class,
            KelasSeeder::class,
            MataPelajaranSeeder::class,
            GuruSeeder::class,
            SiswaSeeder::class,
            JadwalSeeder::class,
            KehadiranSeeder::class,
            IzinGuruSeeder::class,
            GuruPenggantiSeeder::class,
        ]);

        $this->command->info('🎉 All seeders completed successfully!');
        $this->command->info('📋 Summary:');
        $this->command->info('   - 10 Users created (admin, kepsek, kurikulum, guru, siswa)');
        $this->command->info('   - 5 Classes created');
        $this->command->info('   - 10 Subjects created');
        $this->command->info('   - 10 Teachers created');
        $this->command->info('   - 10 Students created');
        $this->command->info('   - 10 Schedule entries created');
        $this->command->info('   - 10 Attendance records created');
        $this->command->info('   - 10 Teacher leave requests created');
        $this->command->info('   - 10 Substitute teacher assignments created');
    }
}