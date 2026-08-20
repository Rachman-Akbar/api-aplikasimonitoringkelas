<?php

namespace App\Filament\Imports;

use App\Models\Guru;
use App\Models\Kelas;
use App\Support\RelationResolver;
use App\Support\TextNormalizer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;

class KelasImporter extends Importer
{
    protected static ?string $model = Kelas::class;

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
            ImportColumn::make('nama')->label('Nama Kelas')->requiredMapping(true)->guess(['nama', 'nama_kelas', 'class_name'])->example('xii rpl 1')->rules(['required', 'string', 'max:255']),
            ImportColumn::make('tingkat')->label('Tingkat')->requiredMapping(true)->guess(['tingkat', 'level', 'grade'])->example('12')->rules(['required', 'integer', 'min:1', 'max:13']),
            ImportColumn::make('jurusan')->label('Jurusan')->requiredMapping(true)->guess(['jurusan', 'major', 'program'])->example('rpl')->rules(['required', 'string', 'max:255']),
            ImportColumn::make('wali_kelas_id')->label('Wali Kelas')->guess(['wali_kelas', 'nama_wali_kelas', 'wali_kelas_id'])->example('Budi Santoso')->rules(['nullable', 'integer', 'exists:gurus,id']),
            ImportColumn::make('kapasitas')->label('Kapasitas')->requiredMapping(true)->guess(['kapasitas', 'capacity'])->example('36')->rules(['required', 'integer', 'min:0']),
            ImportColumn::make('jumlah_siswa')->label('Jumlah Siswa')->guess(['jumlah_siswa', 'jml_siswa'])->example('0')->rules(['nullable', 'integer', 'min:0']),
            ImportColumn::make('ruangan')->label('Ruangan')->guess(['ruangan', 'room'])->example('ruang 301')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('status')->label('Status')->guess(['status', 'Status'])->example('aktif')->rules(['nullable', 'in:aktif,nonaktif']),
        ];
    }

    public function resolveRecord(): ?Kelas
    {
        $nama = TextNormalizer::lower($this->data['nama'] ?? null);

        if ($nama) {
            $record = Kelas::withTrashed()->whereRaw('LOWER(TRIM(nama)) = ?', [$nama])->first();

            if ($record) {
                return $record;
            }
        }

        return new Kelas();
    }

    public function beforeValidate(): void
    {
        $this->data['nama'] = TextNormalizer::lower($this->data['nama'] ?? null);
        $this->data['jurusan'] = TextNormalizer::lower($this->data['jurusan'] ?? null);
        $this->data['ruangan'] = TextNormalizer::lower($this->data['ruangan'] ?? null);
        $this->data['status'] = TextNormalizer::lower($this->data['status'] ?? 'aktif');
        $this->data['jumlah_siswa'] = isset($this->data['jumlah_siswa']) && $this->data['jumlah_siswa'] !== '' ? (int) $this->data['jumlah_siswa'] : 0;

        $wali = $this->data['wali_kelas_id'] ?? null;

        if ($wali === null || $wali === '') {
            $this->data['wali_kelas_id'] = null;
        } elseif (is_numeric($wali) && Guru::query()->whereKey((int) $wali)->exists()) {
            $this->data['wali_kelas_id'] = (int) $wali;
        } else {
            $this->data['wali_kelas_id'] = RelationResolver::idByText(Guru::class, 'nama', $wali, 'Wali kelas', false);
        }
    }

    public function beforeSave(): void
    {
        $this->record->fill($this->data);
    }

    public function afterSave(): void
    {
        Log::info('Kelas berhasil diimport', ['id' => $this->record->id, 'nama' => $this->record->nama]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import kelas selesai! ' . number_format($import->successful_rows) . ' baris berhasil diimport.';

        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failed) . ' baris gagal diimport.';
        }

        return $body;
    }
}
