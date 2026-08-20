<?php

namespace App\Imports;

use App\Models\MataPelajaran;
use App\Support\TextNormalizer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MataPelajaranImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    public function model(array $row)
    {
        $kode = TextNormalizer::lower($row['kode'] ?? null);
        $nama = TextNormalizer::lower($row['nama'] ?? null);
        $record = MataPelajaran::withTrashed()->whereRaw('LOWER(TRIM(kode)) = ?', [$kode])->first();

        if (!$record) {
            $record = MataPelajaran::withTrashed()->whereRaw('LOWER(TRIM(nama)) = ?', [$nama])->first();
        }

        $record ??= new MataPelajaran();
        if ($record->exists && $record->trashed()) {
            $record->deleted_at = null;
        }

        $record->fill([
            'kode' => $kode,
            'nama' => $nama,
            'deskripsi' => TextNormalizer::trim($row['deskripsi'] ?? null),
            'sks' => $row['sks'] ?? 1,
            'kategori' => TextNormalizer::lower($row['kategori'] ?? null),
            'status' => TextNormalizer::lower($row['status'] ?? 'aktif'),
        ]);

        return $record;
    }

    public function rules(): array
    {
        return [
            'kode' => 'required|string|max:20',
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:255',
            'sks' => 'nullable|integer|min:0|max:10',
            'kategori' => 'nullable|string|max:50',
            'status' => 'nullable|string',
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
