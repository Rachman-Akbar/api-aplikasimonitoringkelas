<?php

namespace App\Filament\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;

class SiswaImporter extends Importer
{
    protected static ?string $model = \App\Models\Siswa::class;

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
            \Filament\Actions\Imports\ImportColumn::make('nis')
                ->label('NIS')
                ->requiredMapping(true)
                ->guess(['nis', 'NIS', 'Nis', 'student_id'])
                ->example('12345')
                ->rules(['required', 'string', 'max:255']),
            \Filament\Actions\Imports\ImportColumn::make('nisn')
                ->label('NISN')
                ->requiredMapping(true)
                ->guess(['nisn', 'NISN', 'Nisn', 'national_student_id'])
                ->example('0012345678')
                ->rules(['required', 'string', 'max:255']),
            \Filament\Actions\Imports\ImportColumn::make('nama')
                ->label('Nama')
                ->requiredMapping(true)
                ->guess(['nama', 'Nama', 'name', 'Name', 'nama_siswa', 'student_name'])
                ->example('Ani Wulandari')
                ->rules(['required', 'string', 'max:255']),
            \Filament\Actions\Imports\ImportColumn::make('email')
                ->label('Email')
                ->guess(['email', 'Email', 'e-mail', 'student_email'])
                ->example('ani@example.com')
                ->rules(['nullable', 'email', 'max:255']),
            \Filament\Actions\Imports\ImportColumn::make('no_telp')
                ->label('No Telepon')
                ->guess(['no_telp', 'no_hp', 'telepon', 'phone', 'phone_number', 'nomor_telepon'])
                ->example('081234567890')
                ->rules(['nullable', 'string', 'max:255']),
            \Filament\Actions\Imports\ImportColumn::make('kelas_id')
                ->label('Kelas ID')
                ->requiredMapping(true)
                ->guess(['kelas_id', 'id_kelas', 'kelas', 'class_id'])
                ->example('1')
                ->rules(['required', 'integer', 'exists:kelas,id']),
            \Filament\Actions\Imports\ImportColumn::make('alamat')
                ->label('Alamat')
                ->guess(['alamat', 'Alamat', 'address', 'Address'])
                ->example('Jl. Pendidikan No. 45')
                ->rules(['nullable', 'string']),
            \Filament\Actions\Imports\ImportColumn::make('jenis_kelamin')
                ->label('Jenis Kelamin')
                ->requiredMapping(true)
                ->guess(['jenis_kelamin', 'jk', 'gender', 'sex', 'kelamin'])
                ->example('P')
                ->rules(['required', 'in:L,P']),
            \Filament\Actions\Imports\ImportColumn::make('tanggal_lahir')
                ->label('Tanggal Lahir')
                ->guess(['tanggal_lahir', 'tgl_lahir', 'birth_date', 'date_of_birth', 'dob'])
                ->example('2008-05-15')
                ->rules(['nullable', 'date']),
            \Filament\Actions\Imports\ImportColumn::make('nama_orang_tua')
                ->label('Nama Orang Tua')
                ->guess(['nama_orang_tua', 'nama_ortu', 'parent_name', 'wali'])
                ->example('Budi Santoso')
                ->rules(['nullable', 'string', 'max:255']),
            \Filament\Actions\Imports\ImportColumn::make('no_telp_orang_tua')
                ->label('No Telepon Orang Tua')
                ->guess(['no_telp_orang_tua', 'no_hp_ortu', 'parent_phone', 'telepon_wali'])
                ->example('081234567891')
                ->rules(['nullable', 'string', 'max:255']),
            \Filament\Actions\Imports\ImportColumn::make('status')
                ->label('Status')
                ->guess(['status', 'Status', 'student_status', 'is_active'])
                ->example('aktif')
                ->rules(['nullable', 'in:aktif,nonaktif,lulus,pindah']),
        ];
    }

    public function resolveRecord(): ?\App\Models\Siswa
    {
        try {
            // Prioritas 1: Cari by ID jika ada (dari export file)
            if (!empty($this->data['id'])) {
                $record = Siswa::find($this->data['id']);
                if ($record) {
                    Log::info('Updating existing Siswa by ID', ['id' => $this->data['id']]);
                    return $record;
                }
            }

            // Prioritas 2: Cari by NIS (unique field)
            if (!empty($this->data['nis'])) {
                return Siswa::firstOrNew([
                    'nis' => $this->data['nis']
                ]);
            }

            // Prioritas 3: Cari by NISN
            if (!empty($this->data['nisn'])) {
                return Siswa::firstOrNew([
                    'nisn' => $this->data['nisn']
                ]);
            }

            return new Siswa();
        } catch (\Exception $e) {
            Log::error('Error saat resolve record siswa: ' . $e->getMessage(), ['data' => $this->data]);
            return null;
        }
    }

    public function beforeValidate(): void
    {
        // Modify $this->data prior to validation if needed.
    }

    public function afterValidate(): void
    {
        // No-op after validation.
    }

    public function beforeSave(): void
    {
        // Use $this->record to adjust the model before save if necessary.
        if ($this->record) {
            // Ensure status has default value if not provided or handle different status formats
            if (!empty($this->data['status'])) {
                $status = strtolower(trim($this->data['status']));
                if (in_array($status, ['1', 'true', 'yes', 'aktif', 'active'], true)) {
                    $this->record->status = 'aktif';
                } elseif (in_array($status, ['0', 'false', 'no', 'nonaktif', 'inactive'], true)) {
                    $this->record->status = 'nonaktif';
                } elseif (in_array($status, ['lulus', 'lulus', 'graduated'])) {
                    $this->record->status = 'lulus';
                } elseif (in_array($status, ['pindah', 'pindah', 'moved'])) {
                    $this->record->status = 'pindah';
                } else {
                    // If it's already one of the valid statuses, keep it as is
                    $this->record->status = in_array($status, ['aktif', 'nonaktif', 'lulus', 'pindah']) ? $status : 'aktif';
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

            // Ensure kelas_id is properly handled by checking if it exists in the DB
            if (!empty($this->data['kelas_id'])) {
                $kelas = \App\Models\Kelas::find($this->data['kelas_id']);
                if (!$kelas) {
                    // If kelas doesn't exist, we'll handle this in resolveRecord, but set to null as fallback
                    $this->record->kelas_id = null;
                }
            }
        }
    }

    public function afterSave(): void
    {
        // Post-save hook; use $this->record if needed.
        if ($this->record) {
            Log::info('Siswa berhasil diimport', ['id' => $this->record->id, 'nis' => $this->record->nis]);
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import siswa selesai! ';
        $body .= number_format($import->successful_rows) . ' baris berhasil diimport.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal diimport.';
        }

        return $body;
    }
}
