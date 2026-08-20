<?php

namespace App\Filament\Imports;

use App\Models\MataPelajaran;
use App\Support\TextNormalizer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;

class MataPelajaranImporter extends Importer
{
    protected static ?string $model = MataPelajaran::class;

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
            ImportColumn::make('kode')->label('Kode')->requiredMapping(true)->guess(['kode', 'code'])->example('mtk001')->rules(['required', 'string', 'max:20']),
            ImportColumn::make('nama')->label('Nama')->requiredMapping(true)->guess(['nama', 'name', 'nama_mata_pelajaran'])->example('matematika')->rules(['required', 'string', 'max:100']),
            ImportColumn::make('deskripsi')->label('Deskripsi')->guess(['deskripsi', 'description', 'keterangan'])->example('Mata pelajaran dasar matematika')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('sks')->label('SKS')->guess(['sks', 'SKS'])->example('3')->rules(['nullable', 'integer', 'min:0', 'max:10']),
            ImportColumn::make('kategori')->label('Kategori')->guess(['kategori', 'category'])->example('keahlian')->rules(['nullable', 'string', 'max:50']),
            ImportColumn::make('status')->label('Status')->guess(['status', 'Status'])->example('aktif')->rules(['nullable', 'in:aktif,nonaktif']),
        ];
    }

    public function resolveRecord(): ?MataPelajaran
    {
        $kode = TextNormalizer::lower($this->data['kode'] ?? null);

        if ($kode) {
            $record = MataPelajaran::withTrashed()->whereRaw('LOWER(TRIM(kode)) = ?', [$kode])->first();

            if ($record) {
                return $record;
            }
        }

        $nama = TextNormalizer::lower($this->data['nama'] ?? null);

        if ($nama) {
            $record = MataPelajaran::withTrashed()->whereRaw('LOWER(TRIM(nama)) = ?', [$nama])->first();

            if ($record) {
                return $record;
            }
        }

        return new MataPelajaran();
    }

    public function beforeValidate(): void
    {
        $this->data['kode'] = TextNormalizer::lower($this->data['kode'] ?? null);
        $this->data['nama'] = TextNormalizer::lower($this->data['nama'] ?? null);
        $this->data['kategori'] = TextNormalizer::lower($this->data['kategori'] ?? null);
        $this->data['status'] = TextNormalizer::lower($this->data['status'] ?? 'aktif');
        $this->data['deskripsi'] = TextNormalizer::trim($this->data['deskripsi'] ?? null);
        $this->data['sks'] = isset($this->data['sks']) && $this->data['sks'] !== '' ? (int) $this->data['sks'] : 1;
    }

    public function beforeSave(): void
    {
        $this->record->fill($this->data);
    }

    public function afterSave(): void
    {
        Log::info('Mata pelajaran berhasil diimport', ['id' => $this->record->id, 'kode' => $this->record->kode]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import mata pelajaran selesai! ' . number_format($import->successful_rows) . ' baris berhasil diimport.';

        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failed) . ' baris gagal diimport.';
        }

        return $body;
    }
}
