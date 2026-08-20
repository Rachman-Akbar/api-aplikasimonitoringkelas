<?php

namespace App\Filament\Imports;

use App\Models\Jadwal;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class JadwalImporter extends Importer
{
    protected static ?string $model = Jadwal::class;

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
            ImportColumn::make('kelas_id')
                ->label('Kelas ID')
                ->requiredMapping()
                ->guess(['kelas_id', 'id_kelas', 'kelas', 'class_id'])
                ->rules(['required', 'integer', 'exists:kelas,id,deleted_at,NULL']),
            ImportColumn::make('mata_pelajaran_id')
                ->label('Mata Pelajaran ID')
                ->requiredMapping()
                ->guess(['mata_pelajaran_id', 'id_mata_pelajaran', 'mapel_id', 'subject_id'])
                ->rules(['required', 'integer', 'exists:mata_pelajarans,id,deleted_at,NULL']),
            ImportColumn::make('guru_id')
                ->label('Guru ID')
                ->requiredMapping()
                ->guess(['guru_id', 'id_guru', 'teacher_id'])
                ->rules(['required', 'integer', 'exists:gurus,id,deleted_at,NULL']),
            ImportColumn::make('hari')
                ->label('Hari')
                ->requiredMapping()
                ->guess(['hari', 'day', 'Hari', 'Day'])
                ->example('Senin')
                ->rules(['required', 'string', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu']),
            ImportColumn::make('jam_ke')
                ->label('Jam Ke')
                ->requiredMapping()
                ->guess(['jam_ke', 'jam', 'period', 'session'])
                ->rules(['required', 'integer', 'min:1', 'max:15']),
            ImportColumn::make('jam_mulai')
                ->label('Jam Mulai')
                ->requiredMapping()
                ->guess(['jam_mulai', 'waktu_mulai', 'start_time', 'mulai'])
                ->example('07:00:00 AM')
                ->rules(['required', 'string']),
            ImportColumn::make('jam_selesai')
                ->label('Jam Selesai')
                ->requiredMapping()
                ->guess(['jam_selesai', 'waktu_selesai', 'end_time', 'selesai'])
                ->example('07:45:00 AM')
                ->rules(['required', 'string']),
            ImportColumn::make('ruangan')
                ->label('Ruangan')
                ->guess(['ruangan', 'room', 'Ruangan', 'Room', 'kelas_ruangan'])
                ->example('A301')
                ->rules(['nullable', 'string', 'max:50']),
            ImportColumn::make('status')
                ->label('Status')
                ->guess(['status', 'Status', 'is_active', 'active'])
                ->example('aktif')
                ->rules(['nullable', 'in:aktif,libur,dibatalkan']),
        ];
    }

    public function resolveRecord(): ?Jadwal
    {
        // Prioritas 1: Cari by ID jika ada (dari export file)
        if (!empty($this->data['id'])) {
            $record = Jadwal::find($this->data['id']);
            if ($record) {
                \Illuminate\Support\Facades\Log::info('Updating existing Jadwal by ID', ['id' => $this->data['id']]);
                return $record;
            }
        }

        // Prioritas 2: Cari by unique combination (kelas, mapel, guru, hari, jam_ke)
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

    protected static function normalizeTime(?string $time): ?string
    {
        if (empty($time)) {
            return null;
        }

        $time = trim($time);

        // Format: 07:00:00 AM atau 07:00 AM (dengan AM/PM)
        if (preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?\s*(AM|PM|am|pm)$/i', $time, $matches)) {
            $hour = (int) $matches[1];
            $minute = $matches[2];
            $second = $matches[3] ?? '00';
            $period = strtoupper($matches[4]);

            // Konversi ke format 24 jam untuk penyimpanan internal jika diperlukan
            // Atau langsung return format AM/PM
            return sprintf('%02d:%s:%s %s', $hour, $minute, $second, $period);
        }

        // Format: HH:MM:SS (24 jam)
        if (preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $time)) {
            return $time;
        }

        // Format: HH:MM (24 jam), tambahkan :00
        if (preg_match('/^\d{1,2}:\d{2}$/', $time)) {
            return $time . ':00';
        }

        return $time;
    }

    public function beforeSave(): void
    {
        // Normalize time format
        if (isset($this->data['jam_mulai'])) {
            $this->data['jam_mulai'] = static::normalizeTime($this->data['jam_mulai']);
        }

        if (isset($this->data['jam_selesai'])) {
            $this->data['jam_selesai'] = static::normalizeTime($this->data['jam_selesai']);
        }

        // Cast jam_ke to integer
        if (isset($this->data['jam_ke'])) {
            $this->data['jam_ke'] = (int) $this->data['jam_ke'];
        }

        // Set default status
        if (empty($this->data['status'])) {
            $this->data['status'] = 'aktif';
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import jadwal selesai!';

        $successful = $import->successful_rows;
        $failed = $import->total_rows - $successful;

        if ($failed > 0) {
            return "{$body} {$successful} data berhasil diimport, {$failed} data gagal.";
        }

        return "{$body} {$successful} data berhasil diimport.";
    }
}
