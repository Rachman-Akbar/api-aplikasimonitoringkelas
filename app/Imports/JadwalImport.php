<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Support\RelationResolver;
use App\Support\TextNormalizer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class JadwalImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    public function model(array $row)
    {
        $kelasId = $this->resolveId(Kelas::class, 'nama', $row['kelas'] ?? $row['kelas_id'] ?? null, 'Kelas');
        $mataPelajaranId = $this->resolveId(MataPelajaran::class, 'nama', $row['mata_pelajaran'] ?? $row['mata_pelajaran_id'] ?? null, 'Mata pelajaran');
        $guruId = $this->resolveId(Guru::class, 'nama', $row['guru'] ?? $row['guru_id'] ?? null, 'Guru');
        $hari = $this->normalizeHari($row['hari'] ?? null);
        $jamKe = (int) ($row['jam_ke'] ?? 0);

        $record = Jadwal::firstOrNew([
            'kelas_id' => $kelasId,
            'mata_pelajaran_id' => $mataPelajaranId,
            'guru_id' => $guruId,
            'hari' => $hari,
            'jam_ke' => $jamKe,
        ]);

        $record->fill([
            'jam_mulai' => $this->normalizeTime($row['jam_mulai'] ?? null),
            'jam_selesai' => $this->normalizeTime($row['jam_selesai'] ?? null),
            'tahun_ajaran' => TextNormalizer::trim($row['tahun_ajaran'] ?? null),
            'ruangan' => TextNormalizer::lower($row['ruangan'] ?? null),
            'status' => TextNormalizer::lower($row['status'] ?? 'aktif'),
            'keterangan' => TextNormalizer::trim($row['keterangan'] ?? null),
        ]);

        return $record;
    }

    private function resolveId(string $modelClass, string $column, mixed $value, string $label): int
    {
        if (is_numeric($value) && $modelClass::query()->whereKey((int) $value)->exists()) {
            return (int) $value;
        }

        return RelationResolver::idByText($modelClass, $column, $value, $label);
    }

    private function normalizeHari(mixed $value): ?string
    {
        return match (TextNormalizer::lower($value)) {
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

    public function rules(): array
    {
        return [
            'kelas' => 'required_without:kelas_id',
            'mata_pelajaran' => 'required_without:mata_pelajaran_id',
            'guru' => 'required_without:guru_id',
            'hari' => 'required|string',
            'jam_ke' => 'required|integer|min:1|max:15',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
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
