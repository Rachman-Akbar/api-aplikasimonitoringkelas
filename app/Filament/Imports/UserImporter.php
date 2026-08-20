<?php

namespace App\Filament\Imports;

use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class UserImporter extends Importer
{
    protected static ?string $model = \App\Models\User::class;

    public static function getAcceptedFileTypes(): array
    {
        return [
            'text/csv',
            'text/plain',
            'text/x-csv',
            'application/csv',
            'application/x-csv',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/comma-separated-values',
            'text/x-comma-separated-values',
        ];
    }

    public static function getColumns(): array
    {
        return [
            \Filament\Actions\Imports\ImportColumn::make('name')
                ->label('Name')
                ->requiredMapping(true)
                ->guess(['name', 'Name', 'nama', 'Nama', 'username'])
                ->example('John Doe')
                ->rules(['required', 'string', 'max:255']),
            \Filament\Actions\Imports\ImportColumn::make('email')
                ->label('Email')
                ->requiredMapping(true)
                ->guess(['email', 'Email', 'e-mail', 'user_email'])
                ->example('john@example.com')
                ->rules(['required', 'email', 'max:255']),
            \Filament\Actions\Imports\ImportColumn::make('password')
                ->label('Password')
                ->guess(['password', 'Password', 'pwd', 'pass'])
                ->example('password123')
                ->rules(['nullable', 'string', 'min:8']),
            \Filament\Actions\Imports\ImportColumn::make('role')
                ->label('Role')
                ->requiredMapping(true)
                ->guess(['role', 'Role', 'roles', 'user_role'])
                ->example('guru')
                ->rules(['required', 'string', 'in:admin,kepsek,kurikulum,guru,siswa']),
        ];
    }

    public function resolveRecord(): ?\App\Models\User
    {
        // Prioritas 1: Cari by ID jika ada (dari export file)
        if (!empty($this->data['id'])) {
            $record = \App\Models\User::find($this->data['id']);
            if ($record) {
                Log::info('Updating existing User by ID', ['id' => $this->data['id']]);
                return $record;
            }
        }

        // Prioritas 2: Cari by email (unique field)
        if (!empty($this->data['email'])) {
            return \App\Models\User::firstOrNew([
                'email' => $this->data['email']
            ]);
        }

        return new \App\Models\User();
    }

    public function beforeValidate(): void
    {
        // You can modify $this->data (array) before validation
        if (! isset($this->data['password']) || empty($this->data['password'])) {
            $this->data['password'] = 'password123'; // Default password if not provided
        }
    }

    public function beforeSave(): void
    {
        // Use $this->record to adjust the model before save if necessary.
        if ($this->record) {
            // Ensure role has valid value or handle different role formats
            if (!empty($this->data['role'])) {
                $role = strtolower(trim($this->data['role']));
                $validRoles = ['admin', 'kepsek', 'kurikulum', 'guru', 'siswa'];

                if (in_array($role, $validRoles)) {
                    $this->record->role = $role;
                } else {
                    // Try to match common variations
                    if (in_array($role, ['administrator', 'administrasi'])) {
                        $this->record->role = 'admin';
                    } elseif (in_array($role, ['kasek', 'kepsek'])) {
                        $this->record->role = 'kepsek';
                    } elseif (in_array($role, ['kurikulum'])) {
                        $this->record->role = 'kurikulum';
                    } elseif (in_array($role, ['teacher'])) {
                        $this->record->role = 'guru';
                    } elseif (in_array($role, ['student', 'murid'])) {
                        $this->record->role = 'siswa';
                    } else {
                        // Default to 'guru' if invalid role provided
                        $this->record->role = 'guru';
                    }
                }
            } else {
                // Default to 'guru' if no role provided
                $this->record->role = 'guru';
            }

            // Hash the password hanya untuk record baru atau jika password di-update
            if (!$this->record->exists || (!empty($this->data['password']) && $this->data['password'] !== 'password123')) {
                if (!str_starts_with($this->data['password'] ?? '', '$2y$')) {
                    $this->record->password = bcrypt($this->data['password'] ?? 'password123');
                }
            }
            // Jika record sudah ada dan password kosong/default, keep password lama
            // Tidak perlu set apapun, password tetap dari database
        }
    }

    public function afterValidate(): void
    {
        // No-op after validation; use $this->data if needed.
    }

    public function afterSave(): void
    {
        // Post-create actions can use $this->record (the created model).
        if ($this->record) {
            Log::info('User berhasil diimport', ['id' => $this->record->id, 'email' => $this->record->email]);
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import user selesai! ';
        $body .= number_format($import->successful_rows) . ' baris berhasil diimport.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal diimport.';
        }

        return $body;
    }
}
