<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Support\RelationResolver;
use App\Support\TextNormalizer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    public function model(array $row)
    {
        $nis = TextNormalizer::trim($row['nis'] ?? null);
        $nisn = TextNormalizer::trim($row['nisn'] ?? null);
        $record = Siswa::query()->whereRaw('LOWER(TRIM(nis)) = ?', [mb_strtolower((string) $nis, 'UTF-8')])->first();

        if (!$record && $nisn) {
            $record = Siswa::query()->whereRaw('LOWER(TRIM(nisn)) = ?', [mb_strtolower((string) $nisn, 'UTF-8')])->first();
        }

        $record ??= new Siswa();
        $kelasValue = $row['kelas'] ?? $row['kelas_id'] ?? null;

        $record->fill([
            'nis' => $nis,
            'nisn' => $nisn,
            'nama' => TextNormalizer::trim($row['nama'] ?? null),
            'email' => TextNormalizer::trim($row['email'] ?? null),
            'no_telp' => TextNormalizer::trim($row['no_telp'] ?? null),
            'alamat' => TextNormalizer::trim($row['alamat'] ?? null),
            'jenis_kelamin' => strtoupper((string) TextNormalizer::trim($row['jenis_kelamin'] ?? null)),
            'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
            'foto' => $row['foto'] ?? null,
            'kelas_id' => $this->resolveKelas($kelasValue),
            'nama_orang_tua' => TextNormalizer::trim($row['nama_orang_tua'] ?? null),
            'no_telp_orang_tua' => TextNormalizer::trim($row['no_telp_orang_tua'] ?? null),
            'status' => TextNormalizer::lower($row['status'] ?? 'aktif'),
        ]);

        return $record;
    }

    private function resolveKelas(mixed $value): int
    {
        if (is_numeric($value) && Kelas::query()->whereKey((int) $value)->exists()) {
            return (int) $value;
        }

        return RelationResolver::idByText(Kelas::class, 'nama', $value, 'Kelas');
    }

    public function rules(): array
    {
        return [
            'nis' => 'required|string|max:255',
            'nisn' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'jenis_kelamin' => 'required|string',
            'tanggal_lahir' => 'nullable|date',
            'kelas' => 'required_without:kelas_id',
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
