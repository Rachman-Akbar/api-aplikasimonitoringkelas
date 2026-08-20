<?php

namespace App\Filament\Imports;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Support\RelationResolver;
use App\Support\TextNormalizer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class JadwalImporter extends Importer
{
    protected static ?string $model = Jadwal::class;

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
            ImportColumn::make('kelas_id')->label('Kelas')->requiredMapping()->guess(['kelas', 'nama_kelas', 'kelas_id'])->example('x rpl 1')->rules(['required', 'integer', 'exists:kelas,id']),
            ImportColumn::make('mata_pelajaran_id')->label('Mata Pelajaran')->requiredMapping()->guess(['mata_pelajaran', 'mapel', 'nama_mata_pelajaran', 'mata_pelajaran_id'])->example('matematika')->rules(['required', 'integer', 'exists:mata_pelajarans,id']),
            ImportColumn::make('guru_id')->label('Guru')->requiredMapping()->guess(['guru', 'nama_guru', 'guru_id'])->example('Budi Santoso')->rules(['required', 'integer', 'exists:gurus,id']),
            ImportColumn::make('hari')->label('Hari')->requiredMapping()->guess(['hari', 'day'])->example('Senin')->rules(['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu']),
            ImportColumn::make('jam_ke')->label('Jam Ke')->requiredMapping()->guess(['jam_ke', 'jam', 'period'])->example('1')->rules(['required', 'integer', 'min:1', 'max:15']),
            ImportColumn::make('jam_mulai')->label('Jam Mulai')->requiredMapping()->guess(['jam_mulai', 'waktu_mulai', 'start_time'])->example('07:00')->rules(['required', 'string']),
            ImportColumn::make('jam_selesai')->label('Jam Selesai')->requiredMapping()->guess(['jam_selesai', 'waktu_selesai', 'end_time'])->example('07:45')->rules(['required', 'string']),
            ImportColumn::make('tahun_ajaran')->label('Tahun Ajaran')->guess(['tahun_ajaran', 'academic_year'])->example('2026/2027')->rules(['nullable', 'string', 'max:20']),
            ImportColumn::make('ruangan')->label('Ruangan')->guess(['ruangan', 'room'])->example('ruang 101')->rules(['nullable', 'string', 'max:50']),
            ImportColumn::make('status')->label('Status')->guess(['status', 'Status'])->example('aktif')->rules(['nullable', 'in:aktif,nonaktif,libur,dibatalkan']),
            ImportColumn::make('keterangan')->label('Keterangan')->guess(['keterangan', 'catatan', 'notes'])->rules(['nullable', 'string']),
        ];
    }

    public function resolveRecord(): ?Jadwal
    {
        $this->prepareData();

        if (
            !empty($this->data['kelas_id']) &&
            !empty($this->data['mata_pelajaran_id']) &&
            !empty($this->data['guru_id']) &&
            !empty($this->data['hari']) &&
            !empty($this->data['jam_ke'])
        ) {
            return Jadwal::firstOrNew([
                'kelas_id' => $this->data['kelas_id'],
                'mata_pelajaran_id' => $this->data['mata_pelajaran_id'],
                'guru_id' => $this->data['guru_id'],
                'hari' => $this->data['hari'],
                'jam_ke' => $this->data['jam_ke'],
            ]);
        }

        return new Jadwal();
    }

    public function beforeValidate(): void
    {
        $this->prepareData();
    }

    public function beforeSave(): void
    {
        $this->record->fill($this->data);
    }

    private function prepareData(): void
    {
        $this->data['kelas_id'] = $this->resolveRelationId(Kelas::class, 'nama', $this->data['kelas_id'] ?? null, 'Kelas');
        $this->data['mata_pelajaran_id'] = $this->resolveRelationId(MataPelajaran::class, 'nama', $this->data['mata_pelajaran_id'] ?? null, 'Mata pelajaran');
        $this->data['guru_id'] = $this->resolveRelationId(Guru::class, 'nama', $this->data['guru_id'] ?? null, 'Guru');
        $this->data['hari'] = $this->normalizeHari($this->data['hari'] ?? null);
        $this->data['jam_ke'] = isset($this->data['jam_ke']) && $this->data['jam_ke'] !== '' ? (int) $this->data['jam_ke'] : null;
        $this->data['jam_mulai'] = $this->normalizeTime($this->data['jam_mulai'] ?? null);
        $this->data['jam_selesai'] = $this->normalizeTime($this->data['jam_selesai'] ?? null);
        $this->data['tahun_ajaran'] = TextNormalizer::trim($this->data['tahun_ajaran'] ?? null);
        $this->data['ruangan'] = TextNormalizer::lower($this->data['ruangan'] ?? null);
        $this->data['status'] = TextNormalizer::lower($this->data['status'] ?? 'aktif');
        $this->data['keterangan'] = TextNormalizer::trim($this->data['keterangan'] ?? null);
    }

    private function resolveRelationId(string $modelClass, string $column, mixed $value, string $label): int
    {
        if (is_numeric($value) && $modelClass::query()->whereKey((int) $value)->exists()) {
            return (int) $value;
        }

        return RelationResolver::idByText($modelClass, $column, $value, $label);
    }

    private function normalizeHari(mixed $value): ?string
    {
        $lower = TextNormalizer::lower($value);

        return match ($lower) {
            'senin' => 'Senin',
            'selasa' => 'Selasa',
            'rabu' => 'Rabu',
            'kamis' => 'Kamis',
            'jumat', "jum'at" => 'Jumat',
            'sabtu' => 'Sabtu',
            'minggu' => 'Minggu',
            default => TextNormalizer::trim($value),
        };
    }

    private function normalizeTime(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $time = trim((string) $value);

        foreach (['H:i:s', 'H:i', 'h:i:s A', 'h:i A'] as $format) {
            $date = \DateTime::createFromFormat($format, $time);

            if ($date !== false) {
                return $date->format('H:i:s');
            }
        }

        return $time;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import jadwal selesai! ' . number_format($import->successful_rows) . ' baris berhasil diimport.';

        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failed) . ' baris gagal diimport.';
        }

        return $body;
    }
}
