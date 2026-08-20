<?php

namespace App\Filament\Imports;

use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class GuruImporter extends Importer
{
    protected static ?string $model = \App\Models\Guru::class;

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
            \Filament\Actions\Imports\ImportColumn::make('nip')
                ->label('NIP')
                ->requiredMapping(true)
                ->guess(['nip', 'NIP', 'Nip', 'teacher_id', 'id_guru'])
                ->example('197501012005011001')
                ->rules(['required', 'string', 'max:255']),
            \Filament\Actions\Imports\ImportColumn::make('nama')
                ->label('Nama')
                ->requiredMapping(true)
                ->guess(['nama', 'Nama', 'name', 'Name', 'nama_guru', 'teacher_name'])
                ->example('Budi Santoso')
                ->rules(['required', 'string', 'max:255']),
            \Filament\Actions\Imports\ImportColumn::make('email')
                ->label('Email')
                ->requiredMapping(true)
                ->guess(['email', 'Email', 'e-mail', 'teacher_email'])
                ->example('budi@example.com')
                ->rules(['required', 'email', 'max:255']),
            \Filament\Actions\Imports\ImportColumn::make('no_telp')
                ->label('No Telepon')
                ->guess(['no_telp', 'no_hp', 'telepon', 'phone', 'phone_number', 'nomor_telepon'])
                ->example('081234567890')
                ->rules(['nullable', 'string', 'max:255']),
            \Filament\Actions\Imports\ImportColumn::make('alamat')
                ->label('Alamat')
                ->guess(['alamat', 'Alamat', 'address', 'Address'])
                ->example('Jl. Merdeka No. 123')
                ->rules(['nullable', 'string']),
            \Filament\Actions\Imports\ImportColumn::make('jenis_kelamin')
                ->label('Jenis Kelamin')
                ->requiredMapping(true)
                ->guess(['jenis_kelamin', 'jk', 'gender', 'sex', 'kelamin'])
                ->example('L')
                ->rules(['required', 'string', 'in:L,P']),
            \Filament\Actions\Imports\ImportColumn::make('tanggal_lahir')
                ->label('Tanggal Lahir')
                ->guess(['tanggal_lahir', 'tgl_lahir', 'birth_date', 'date_of_birth', 'dob'])
                ->example('1975-01-01')
                ->rules(['nullable', 'date']),
            \Filament\Actions\Imports\ImportColumn::make('status')
                ->label('Status')
                ->guess(['status', 'Status', 'is_active', 'active'])
                ->example('aktif')
                ->rules(['nullable', 'in:aktif,nonaktif']),
        ];
    }

    public function resolveRecord(): ?\App\Models\Guru
    {
        // Prioritas 1: Cari by ID jika ada (dari export file)
        if (!empty($this->data['id'])) {
            $record = \App\Models\Guru::find($this->data['id']);
            if ($record) {
                Log::info('Updating existing Guru by ID', ['id' => $this->data['id']]);
                return $record;
            }
        }

        // Prioritas 2: Cari by NIP (unique field)
        if (!empty($this->data['nip'])) {
            return \App\Models\Guru::firstOrNew([
                'nip' => $this->data['nip']
            ]);
        }

        // Prioritas 3: Cari by email
        if (!empty($this->data['email'])) {
            return \App\Models\Guru::firstOrNew([
                'email' => $this->data['email']
            ]);
        }

        return new \App\Models\Guru();
    }

    public function beforeValidate(): void
    {
        // You can modify $this->data (array) before validation if needed.
        // Example: normalize values or map alternative headers.
        // $data = $this->data;
        // $this->data = $data;
    }

    public function afterValidate(): void
    {
        // No-op by default. Use $this->data to inspect/modify after validation.
    }

    public function beforeSave(): void
    {
        // Access and modify the model instance via $this->record if needed.
        if ($this->record) {
            // Ensure status has default value if not provided or handle different status formats
            if (!empty($this->data['status'])) {
                $status = strtolower(trim($this->data['status']));
                if (in_array($status, ['1', 'true', 'yes', 'aktif', 'active'], true)) {
                    $this->record->status = 'aktif';
                } elseif (in_array($status, ['0', 'false', 'no', 'nonaktif', 'inactive'], true)) {
                    $this->record->status = 'nonaktif';
                } else {
                    // If it's already 'aktif' or 'nonaktif', keep it as is
                    $this->record->status = in_array($status, ['aktif', 'nonaktif']) ? $status : 'aktif';
                }
            } else {
                // Default to aktif if no status provided
                $this->record->status = 'aktif';
            }

            // Ensure jenis_kelamin is properly formatted
            if (!empty($this->data['jenis_kelamin'])) {
                $jk = strtoupper(trim($this->data['jenis_kelamin']));
                if (!in_array($jk, ['L', 'P'])) {
                    // Try to convert from common formats
                    if (in_array(strtolower($jk), ['laki-laki', 'laki laki', 'male', 'm'])) {
                        $this->record->jenis_kelamin = 'L';
                    } elseif (in_array(strtolower($jk), ['perempuan', 'female', 'f'])) {
                        $this->record->jenis_kelamin = 'P';
                    } else {
                        $this->record->jenis_kelamin = 'L'; // default to L
                    }
                } else {
                    $this->record->jenis_kelamin = $jk;
                }
            }
        }
    }

    public function afterSave(): void
    {
        // Use $this->record for post-save actions if necessary.
        if ($this->record) {
            Log::info('Guru berhasil diimport', ['id' => $this->record->id, 'nip' => $this->record->nip]);
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import guru selesai! ';
        $body .= number_format($import->successful_rows) . ' baris berhasil diimport.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal diimport.';
        }

        return $body;
    }
}
