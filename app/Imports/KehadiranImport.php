<?php

namespace App\Imports;

use App\Models\Kehadiran;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class KehadiranImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnError
{
    use SkipsFailures;

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Kehadiran([
            'siswa_id' => $row['siswa_id'] ?? null,
            'jadwal_id' => $row['jadwal_id'] ?? null,
            'tanggal' => $row['tanggal'] ?? null,
            'status' => $row['status'] ?? 'hadir',
            'keterangan' => $row['keterangan'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'siswa_id' => 'required|exists:siswas,id',
            'jadwal_id' => 'required|exists:jadwals,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,tidak_hadir,izin,sakit',
            'keterangan' => 'nullable|string|max:500',
        ];
    }

    public function onError(\Throwable $e)
    {
        // Handle error
        return null;
    }
}
