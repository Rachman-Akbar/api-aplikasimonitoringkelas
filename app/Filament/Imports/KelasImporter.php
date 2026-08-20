<?php

namespace App\Filament\Imports;

use App\Models\Kelas;
use App\Models\Guru;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;

class KelasImporter extends Importer
{
    protected static ?string $model = \App\Models\Kelas::class;

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
            \Filament\Actions\Imports\ImportColumn::make('nama')
                ->label('Nama Kelas')
                ->requiredMapping(true)
                ->guess(['nama', 'Nama', 'name', 'Name', 'nama_kelas', 'class_name'])
                ->example('XII RPL 1')
                ->rules(['required', 'string', 'max:255']),
            \Filament\Actions\Imports\ImportColumn::make('tingkat')
                ->label('Tingkat')
                ->requiredMapping(true)
                ->guess(['tingkat', 'Tingkat', 'level', 'grade', 'kelas'])
                ->example('12')
                ->rules(['required', 'integer', 'min:1', 'max:13']),
            \Filament\Actions\Imports\ImportColumn::make('jurusan')
                ->label('Jurusan')
                ->requiredMapping(true)
                ->guess(['jurusan', 'Jurusan', 'major', 'program', 'prodi'])
                ->example('Rekayasa Perangkat Lunak')
                ->rules(['required', 'string', 'max:255']),
            \Filament\Actions\Imports\ImportColumn::make('wali_kelas_id')
                ->label('Wali Kelas ID')
                ->guess(['wali_kelas_id', 'id_wali_kelas', 'guru_id', 'homeroom_teacher_id'])
                ->example('1')
                ->rules(['nullable', 'integer', 'exists:gurus,id']),
            \Filament\Actions\Imports\ImportColumn::make('kapasitas')
                ->label('Kapasitas')
                ->requiredMapping(true)
                ->guess(['kapasitas', 'Kapasitas', 'capacity', 'max_capacity', 'max_siswa'])
                ->example('36')
                ->rules(['required', 'integer', 'min:0']),
            \Filament\Actions\Imports\ImportColumn::make('jumlah_siswa')
                ->label('Jumlah Siswa')
                ->requiredMapping(true)
                ->guess(['jumlah_siswa', 'jml_siswa', 'student_count', 'total_siswa'])
                ->example('32')
                ->rules(['required', 'integer', 'min:0']),
            \Filament\Actions\Imports\ImportColumn::make('ruangan')
                ->label('Ruangan')
                ->guess(['ruangan', 'Ruangan', 'room', 'classroom', 'kelas_ruangan'])
                ->example('A301')
                ->rules(['nullable', 'string', 'max:255']),
            \Filament\Actions\Imports\ImportColumn::make('status')
                ->label('Status')
                ->guess(['status', 'Status', 'is_active', 'active'])
                ->example('aktif')
                ->rules(['nullable', 'in:aktif,nonaktif']),
        ];
    }

    public function resolveRecord(): ?\App\Models\Kelas
    {
        // Prioritas 1: Cari by ID jika ada (dari export file)
        if (!empty($this->data['id'])) {
            $record = Kelas::find($this->data['id']);
            if ($record) {
                Log::info('Updating existing Kelas by ID', ['id' => $this->data['id']]);
                return $record;
            }
        }

        // Prioritas 2: Cari by nama (unique field)
        if (!empty($this->data['nama'])) {
            return Kelas::firstOrNew([
                'nama' => $this->data['nama']
            ]);
        }

        return new Kelas();
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
                } else {
                    // If it's already one of the valid statuses, keep it as is
                    $this->record->status = in_array($status, ['aktif', 'nonaktif']) ? $status : 'aktif';
                }
            } else {
                // Default to aktif if no status provided
                $this->record->status = 'aktif';
            }

            // Ensure numeric fields are properly converted
            if (isset($this->data['tingkat'])) {
                $this->record->tingkat = (int) $this->data['tingkat'];
            }

            if (isset($this->data['kapasitas'])) {
                $this->record->kapasitas = (int) $this->data['kapasitas'];
            }

            if (isset($this->data['jumlah_siswa'])) {
                $this->record->jumlah_siswa = (int) $this->data['jumlah_siswa'];
            }

            if (isset($this->data['wali_kelas_id'])) {
                $this->record->wali_kelas_id = (int) $this->data['wali_kelas_id'];
            }
        }
    }

    public function afterSave(): void
    {
        // Post-save hook; use $this->record if needed.
        if ($this->record) {
            Log::info('Kelas berhasil diimport', ['id' => $this->record->id, 'nama' => $this->record->nama]);
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import kelas selesai! ';
        $body .= number_format($import->successful_rows) . ' baris berhasil diimport.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal diimport.';
        }

        return $body;
    }
}
