<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BasicTestDataSeeder::class,
            JadwalTestSeeder::class,  // This creates jadwal entries which need basic data first
            IzinGuruSeeder::class,
            GuruPenggantiSeeder::class,
            KehadiranGuruSeeder::class,
            KehadiranSeeder::class,
            AttendanceAndScheduleSeeder::class,
        ]);
    }
}
