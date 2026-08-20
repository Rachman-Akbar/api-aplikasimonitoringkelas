<?php

namespace App\Imports;

use App\Models\MataPelajaran;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class MataPelajaranImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnError
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
        return new MataPelajaran([
            'kode' => $row['kode'] ?? null,
            'nama' => $row['nama'] ?? null,
            'deskripsi' => $row['deskripsi'] ?? null,
            'sks' => $row['sks'] ?? 1,
            'kategori' => $row['kategori'] ?? 'wajib',
            'status' => $this->parseStatus($row['status'] ?? 'aktif'),
        ]);
    }

    private function parseStatus($value)
    {
        if (is_string($value)) {
            $value = strtolower(trim($value));
            if (in_array($value, ['1', 'true', 'yes', 'aktif', 'active'], true)) {
                return 'aktif';
            }
            if (in_array($value, ['0', 'false', 'no', 'nonaktif', 'inactive'], true)) {
                return 'nonaktif';
            }
        }

        // Default to aktif if value is truthy, nonaktif if falsy
        return $value ? 'aktif' : 'nonaktif';
    }

    // For imports, perform normalization/casting inside model() if needed.

    public function rules(): array
    {
        return [
            'kode' => 'required|string|max:20|unique:mata_pelajarans,kode',
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:255',
            // Make sks/kategori/status nullable so imports without these columns
            // will still succeed and use sensible defaults in model().
            'sks' => 'nullable|integer|min:1|max:10',
            'kategori' => 'nullable|in:wajib,pilihan,muatan-lokal',
            'status' => 'nullable|in:aktif,nonaktif',
        ];
    }

    public function onError(\Throwable $e)
    {
        // Handle error
        return null;
    }
}
