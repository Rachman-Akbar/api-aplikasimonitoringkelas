<?php

namespace App\Imports;

use App\Models\IzinGuru;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class IzinGuruImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnError
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
        return new IzinGuru([
            'guru_id' => $row['guru_id'] ?? null,
            'jenis_izin' => $row['jenis_izin'] ?? 'izin',
            'tanggal_mulai' => $row['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $row['tanggal_selesai'] ?? null,
            'alasan' => $row['alasan'] ?? null,
            'file_surat' => $row['file_surat'] ?? null,
            'status_approval' => $row['status_approval'] ?? 'menunggu',
            'disetujui_oleh' => !empty($row['disetujui_oleh']) ? $row['disetujui_oleh'] : null,
            'tanggal_approval' => !empty($row['tanggal_approval']) ? $row['tanggal_approval'] : null,
            'catatan_approval' => $row['catatan_approval'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'guru_id' => 'required|exists:gurus,id',
            'jenis_izin' => 'required|in:sakit,izin,cuti,lainnya',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|max:500',
            'file_surat' => 'nullable|string|max:255',
            'status_approval' => 'nullable|in:menunggu,disetujui,ditolak',
            'disetujui_oleh' => 'nullable|exists:users,id',
            'tanggal_approval' => 'nullable|date',
            'catatan_approval' => 'nullable|string|max:500',
        ];
    }

    public function onError(\Throwable $e)
    {
        // Handle error
        return null;
    }
}
