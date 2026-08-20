<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            'admin@sekolah.com',
            'kepsek@sekolah.com',
            'kurikulum@sekolah.com',
            'budi.guru@sekolah.com',
            'siti.guru@sekolah.com',
            'andi.siswa@sekolah.com',
            'siti.siswa@sekolah.com',
        ];

        foreach ($users as $email) {
            User::where('email', $email)->update([
                'password' => Hash::make('password')
            ]);
        }

        $this->command->info('All user passwords have been reset to: password');
    }
}
