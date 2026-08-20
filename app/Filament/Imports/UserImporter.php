<?php

namespace App\Filament\Imports;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\User;
use App\Support\RelationResolver;
use App\Support\TextNormalizer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getAcceptedFileTypes(): array
    {
        return [
            'text/csv', 'text/plain', 'text/x-csv', 'application/csv', 'application/x-csv',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/comma-separated-values', 'text/x-comma-separated-values',
        ];
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')->label('Name')->requiredMapping(true)->guess(['name', 'nama'])->example('John Doe')->rules(['required', 'string', 'max:255']),
            ImportColumn::make('email')->label('Email')->requiredMapping(true)->guess(['email', 'e-mail'])->example('john@example.com')->rules(['required', 'email', 'max:255']),
            ImportColumn::make('password')->label('Password')->guess(['password', 'pwd', 'pass'])->example('password123')->rules(['nullable', 'string', 'min:8']),
            ImportColumn::make('role')->label('Role')->requiredMapping(true)->guess(['role', 'roles'])->example('guru')->rules(['required', 'in:admin,kepsek,kurikulum,guru,siswa']),
            ImportColumn::make('guru_id')->label('Guru')->guess(['guru', 'nama_guru', 'guru_id'])->example('Budi Santoso')->rules(['nullable', 'integer', 'exists:gurus,id']),
            ImportColumn::make('kelas_id')->label('Kelas')->guess(['kelas', 'nama_kelas', 'kelas_id'])->example('x rpl 1')->rules(['nullable', 'integer', 'exists:kelas,id']),
        ];
    }

    public function resolveRecord(): ?User
    {
        $email = TextNormalizer::trim($this->data['email'] ?? null);

        if ($email) {
            $record = User::query()->whereRaw('LOWER(TRIM(email)) = ?', [mb_strtolower($email, 'UTF-8')])->first();

            if ($record) {
                return $record;
            }
        }

        return new User();
    }

    public function beforeValidate(): void
    {
        $this->data['name'] = TextNormalizer::trim($this->data['name'] ?? null);
        $this->data['email'] = TextNormalizer::trim($this->data['email'] ?? null);
        $this->data['role'] = TextNormalizer::lower($this->data['role'] ?? null);

        $guru = $this->data['guru_id'] ?? null;
        $kelas = $this->data['kelas_id'] ?? null;

        $this->data['guru_id'] = $this->resolveOptionalRelation(Guru::class, 'nama', $guru, 'Guru');
        $this->data['kelas_id'] = $this->resolveOptionalRelation(Kelas::class, 'nama', $kelas, 'Kelas');

        if ($this->data['role'] === 'guru' && !$this->data['guru_id']) {
            throw ValidationException::withMessages(['guru_id' => 'Kolom guru wajib diisi dengan nama guru untuk role guru.']);
        }

        if ($this->data['role'] === 'siswa' && !$this->data['kelas_id']) {
            throw ValidationException::withMessages(['kelas_id' => 'Kolom kelas wajib diisi dengan nama kelas untuk role siswa.']);
        }
    }

    public function beforeSave(): void
    {
        $data = $this->data;
        $password = $data['password'] ?? null;
        unset($data['password']);

        $this->record->fill($data);

        if (!$this->record->exists) {
            if (!$password) {
                throw ValidationException::withMessages(['password' => 'Password wajib diisi untuk user baru.']);
            }

            $this->record->password = Hash::make($password);
        } elseif ($password) {
            $this->record->password = Hash::make($password);
        }
    }

    private function resolveOptionalRelation(string $modelClass, string $column, mixed $value, string $label): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value) && $modelClass::query()->whereKey((int) $value)->exists()) {
            return (int) $value;
        }

        return RelationResolver::idByText($modelClass, $column, $value, $label, false);
    }

    public function afterSave(): void
    {
        Log::info('User berhasil diimport', ['id' => $this->record->id, 'email' => $this->record->email]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import user selesai! ' . number_format($import->successful_rows) . ' baris berhasil diimport.';

        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failed) . ' baris gagal diimport.';
        }

        return $body;
    }
}
