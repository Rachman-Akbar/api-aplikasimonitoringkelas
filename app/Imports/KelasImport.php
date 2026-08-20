<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\Kelas;
use App\Support\RelationResolver;
use App\Support\TextNormalizer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class KelasImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $nama = TextNormalizer::lower($row['nama'] ?? null);
        $record = Kelas::withTrashed()->whereRaw('LOWER(TRIM(nama)) = ?', [$nama])->first() ?? new Kelas();
        $wali = $row['wali_kelas'] ?? $row['wali_kelas_id'] ?? null;

        if ($record->exists && $record->trashed()) {
            $record->deleted_at = null;
        }

        $record->fill([
            'nama' => $nama,
            'tingkat' => $row['tingkat'] ?? null,
            'jurusan' => TextNormalizer::lower($row['jurusan'] ?? null),
            'wali_kelas_id' => $this->resolveGuru($wali),
            'kapasitas' => $row['kapasitas'] ?? 0,
            'jumlah_siswa' => $row['jumlah_siswa'] ?? 0,
            'ruangan' => TextNormalizer::lower($row['ruangan'] ?? null),
            'status' => TextNormalizer::lower($row['status'] ?? 'aktif'),
        ]);

        return $record;
    }

    private function resolveGuru(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value) && Guru::query()->whereKey((int) $value)->exists()) {
            return (int) $value;
        }

        return RelationResolver::idByText(Guru::class, 'nama', $value, 'Wali kelas', false);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'tingkat' => 'required|integer|min:1|max:13',
            'jurusan' => 'required|string|max:255',
            'wali_kelas' => 'nullable|string|max:255',
            'kapasitas' => 'required|integer|min:0',
            'jumlah_siswa' => 'nullable|integer|min:0',
            'ruangan' => 'nullable|string|max:255',
            'status' => 'nullable|string',
        ];
    }
}
