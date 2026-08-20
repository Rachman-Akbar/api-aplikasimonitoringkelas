<?php

namespace App\Filament\Admin\Resources\SiswaResource\Pages;

use App\Filament\Admin\Resources\SiswaResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateSiswa extends CreateRecord
{
    protected static string $resource = SiswaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove create_user_account flag
        $createUserAccount = $data['create_user_account'] ?? false;
        unset($data['create_user_account']);

        // If create user account is checked, create user first
        if ($createUserAccount && !empty($data['password']) && !empty($data['email'])) {
            // Cek apakah email sudah digunakan
            $existingUser = User::where('email', $data['email'])->first();

            if ($existingUser) {
                // Jika user sudah ada, gunakan user yang ada
                $data['user_id'] = $existingUser->id;

                // Tampilkan notifikasi
                \Filament\Notifications\Notification::make()
                    ->warning()
                    ->title('Email Sudah Terdaftar')
                    ->body('Email ' . $data['email'] . ' sudah digunakan oleh user lain. Siswa akan ditautkan ke akun user yang sudah ada.')
                    ->persistent()
                    ->send();
            } else {
                // Buat user baru
                try {
                    $user = User::create([
                        'name' => $data['nama'],
                        'email' => $data['email'],
                        'password' => Hash::make($data['password']),
                        'role' => 'siswa',
                        'kelas_id' => $data['kelas_id'] ?? null,
                    ]);

                    $data['user_id'] = $user->id;

                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Akun User Berhasil Dibuat')
                        ->body('Akun user untuk siswa ' . $data['nama'] . ' telah dibuat dengan email: ' . $data['email'])
                        ->send();
                } catch (\Exception $e) {
                    // Jika terjadi error, set user_id null
                    $data['user_id'] = null;

                    \Filament\Notifications\Notification::make()
                        ->danger()
                        ->title('Gagal Membuat Akun User')
                        ->body('Terjadi kesalahan saat membuat akun user: ' . $e->getMessage())
                        ->persistent()
                        ->send();
                }
            }
        } else {
            $data['user_id'] = null;
        }

        // Remove password fields from siswa data
        unset($data['password']);
        unset($data['password_confirmation']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
