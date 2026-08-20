<?php

namespace App\Filament\Imports;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Support\RelationResolver;
use App\Support\TextNormalizer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;

class SiswaImporter extends Importer
{
    protected static ?string $model = Siswa::class;

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
            ImportColumn::make('nis')->label('NIS')->requiredMapping(true)->guess(['nis', 'NIS'])->example('2026001')->rules(['required', 'string', 'max:255']),
            ImportColumn::make('nisn')->label('NISN')->requiredMapping(true)->guess(['nisn', 'NISN'])->example('0012345678')->rules(['required', 'string', 'max:255']),
            ImportColumn::make('nama')->label('Nama')->requiredMapping(true)->guess(['nama', 'Nama', 'name', 'nama_siswa'])->example('Ani Wulandari')->rules(['required', 'string', 'max:255']),
            ImportColumn::make('email')->label('Email')->guess(['email', 'Email'])->example('ani@example.com')->rules(['nullable', 'email', 'max:255']),
            ImportColumn::make('no_telp')->label('No Telepon')->guess(['no_telp', 'no_hp', 'telepon'])->example('081234567890')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('kelas_id')->label('Kelas')->requiredMapping(true)->guess(['kelas', 'nama_kelas', 'kelas_id'])->example('x rpl 1')->rules(['required', 'integer', 'exists:kelas,id']),
            ImportColumn::make('alamat')->label('Alamat')->guess(['alamat', 'address'])->example('Jl. Pendidikan No. 45')->rules(['nullable', 'string']),
            ImportColumn::make('jenis_kelamin')->label('Jenis Kelamin')->requiredMapping(true)->guess(['jenis_kelamin', 'jk', 'gender'])->example('P')->rules(['required', 'in:L,P']),
            ImportColumn::make('tanggal_lahir')->label('Tanggal Lahir')->guess(['tanggal_lahir', 'tgl_lahir'])->example('2008-05-15')->rules(['nullable', 'date']),
            ImportColumn::make('nama_orang_tua')->label('Nama Orang Tua')->guess(['nama_orang_tua', 'nama_ortu', 'wali'])->example('Budi Santoso')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('no_telp_orang_tua')->label('No Telepon Orang Tua')->guess(['no_telp_orang_tua', 'no_hp_ortu'])->example('081234567891')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('status')->label('Status')->guess(['status', 'Status'])->example('aktif')->rules(['nullable', 'in:aktif,nonaktif,lulus,pindah']),
        ];
    }

    public function resolveRecord(): ?Siswa
    {
        foreach (['nis', 'nisn'] as $field) {
            $value = TextNormalizer::trim($this->data[$field] ?? null);

            if ($value) {
                $record = Siswa::query()->whereRaw("LOWER(TRIM({$field})) = ?", [mb_strtolower($value, 'UTF-8')])->first();

                if ($record) {
                    return $record;
                }
            }
        }

        return new Siswa();
    }

    public function beforeValidate(): void
    {
        foreach (['nis', 'nisn', 'nama', 'email', 'no_telp', 'alamat', 'nama_orang_tua', 'no_telp_orang_tua'] as $field) {
            if (array_key_exists($field, $this->data)) {
                $this->data[$field] = TextNormalizer::trim($this->data[$field]);
            }
        }

        $kelas = $this->data['kelas_id'] ?? null;
        $this->data['kelas_id'] = $this->resolveKelasId($kelas);

        $gender = mb_strtolower((string) ($this->data['jenis_kelamin'] ?? ''), 'UTF-8');
        $this->data['jenis_kelamin'] = match ($gender) {
            'l', 'laki-laki', 'laki laki', 'male', 'm' => 'L',
            'p', 'perempuan', 'female', 'f' => 'P',
            default => strtoupper(trim((string) ($this->data['jenis_kelamin'] ?? ''))),
        };

        $status = TextNormalizer::lower($this->data['status'] ?? 'aktif');
        $this->data['status'] = in_array($status, ['aktif', 'nonaktif', 'lulus', 'pindah'], true) ? $status : 'aktif';
    }

    public function beforeSave(): void
    {
        $this->record->fill($this->data);
    }

    private function resolveKelasId(mixed $value): int
    {
        if (is_numeric($value) && Kelas::query()->whereKey((int) $value)->exists()) {
            return (int) $value;
        }

        return RelationResolver::idByText(Kelas::class, 'nama', $value, 'Kelas');
    }

    public function afterSave(): void
    {
        Log::info('Siswa berhasil diimport', ['id' => $this->record->id, 'nis' => $this->record->nis]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import siswa selesai! ' . number_format($import->successful_rows) . ' baris berhasil diimport.';

        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failed) . ' baris gagal diimport.';
        }

        return $body;
    }
}
