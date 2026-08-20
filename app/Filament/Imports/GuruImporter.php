<?php

namespace App\Filament\Imports;

use App\Models\Guru;
use App\Support\TextNormalizer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;

class GuruImporter extends Importer
{
    protected static ?string $model = Guru::class;

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
            ImportColumn::make('nip')->label('NIP')->requiredMapping(true)->guess(['nip', 'NIP', 'Nip'])->example('197501012005011001')->rules(['required', 'string', 'max:255']),
            ImportColumn::make('nama')->label('Nama')->requiredMapping(true)->guess(['nama', 'Nama', 'name', 'nama_guru'])->example('Budi Santoso')->rules(['required', 'string', 'max:255']),
            ImportColumn::make('email')->label('Email')->requiredMapping(true)->guess(['email', 'Email', 'e-mail'])->example('budi@example.com')->rules(['required', 'email', 'max:255']),
            ImportColumn::make('no_telp')->label('No Telepon')->guess(['no_telp', 'no_hp', 'telepon', 'phone'])->example('081234567890')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('alamat')->label('Alamat')->guess(['alamat', 'Alamat', 'address'])->example('Jl. Merdeka No. 123')->rules(['nullable', 'string']),
            ImportColumn::make('jenis_kelamin')->label('Jenis Kelamin')->requiredMapping(true)->guess(['jenis_kelamin', 'jk', 'gender'])->example('L')->rules(['required', 'in:L,P']),
            ImportColumn::make('tanggal_lahir')->label('Tanggal Lahir')->guess(['tanggal_lahir', 'tgl_lahir', 'birth_date'])->example('1975-01-01')->rules(['nullable', 'date']),
            ImportColumn::make('status')->label('Status')->guess(['status', 'Status'])->example('aktif')->rules(['nullable', 'in:aktif,nonaktif']),
        ];
    }

    public function resolveRecord(): ?Guru
    {
        $nip = TextNormalizer::trim($this->data['nip'] ?? null);

        if ($nip) {
            $record = Guru::query()->whereRaw('LOWER(TRIM(nip)) = ?', [mb_strtolower($nip, 'UTF-8')])->first();

            if ($record) {
                return $record;
            }
        }

        $email = TextNormalizer::trim($this->data['email'] ?? null);

        if ($email) {
            $record = Guru::query()->whereRaw('LOWER(TRIM(email)) = ?', [mb_strtolower($email, 'UTF-8')])->first();

            if ($record) {
                return $record;
            }
        }

        return new Guru();
    }

    public function beforeValidate(): void
    {
        foreach (['nip', 'nama', 'email', 'no_telp', 'alamat'] as $field) {
            if (array_key_exists($field, $this->data)) {
                $this->data[$field] = TextNormalizer::trim($this->data[$field]);
            }
        }

        $gender = mb_strtolower((string) ($this->data['jenis_kelamin'] ?? ''), 'UTF-8');
        $this->data['jenis_kelamin'] = match ($gender) {
            'l', 'laki-laki', 'laki laki', 'male', 'm' => 'L',
            'p', 'perempuan', 'female', 'f' => 'P',
            default => strtoupper(trim((string) ($this->data['jenis_kelamin'] ?? ''))),
        };

        $status = TextNormalizer::lower($this->data['status'] ?? 'aktif');
        $this->data['status'] = in_array($status, ['0', 'false', 'no', 'nonaktif', 'inactive'], true) ? 'nonaktif' : 'aktif';
    }

    public function beforeSave(): void
    {
        $this->record->fill($this->data);
    }

    public function afterSave(): void
    {
        Log::info('Guru berhasil diimport', ['id' => $this->record->id, 'nip' => $this->record->nip]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import guru selesai! ' . number_format($import->successful_rows) . ' baris berhasil diimport.';

        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failed) . ' baris gagal diimport.';
        }

        return $body;
    }
}
