<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\IzinGuru;
use App\Models\User;
use App\Support\RelationResolver;
use App\Support\TextNormalizer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class IzinGuruImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    public function model(array $row)
    {
        $guruValue = $row['guru'] ?? $row['guru_id'] ?? null;
        $approverValue = $row['disetujui_oleh'] ?? null;

        return new IzinGuru([
            'guru_id' => $this->resolveId(Guru::class, 'nama', $guruValue, 'Guru'),
            'jenis_izin' => TextNormalizer::lower($row['jenis_izin'] ?? 'izin'),
            'tanggal_mulai' => $row['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $row['tanggal_selesai'] ?? null,
            'keterangan' => TextNormalizer::trim($row['keterangan'] ?? $row['alasan'] ?? null),
            'file_surat' => TextNormalizer::trim($row['file_surat'] ?? null),
            'status_approval' => $this->normalizeApproval($row['status_approval'] ?? 'pending'),
            'disetujui_oleh' => $this->resolveOptionalUser($approverValue),
            'tanggal_approval' => $row['tanggal_approval'] ?? null,
            'catatan_approval' => TextNormalizer::trim($row['catatan_approval'] ?? null),
        ]);
    }

    private function resolveId(string $modelClass, string $column, mixed $value, string $label): int
    {
        if (is_numeric($value) && $modelClass::query()->whereKey((int) $value)->exists()) {
            return (int) $value;
        }

        return RelationResolver::idByText($modelClass, $column, $value, $label);
    }

    private function resolveOptionalUser(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value) && User::query()->whereKey((int) $value)->exists()) {
            return (int) $value;
        }

        return RelationResolver::idByText(User::class, 'name', $value, 'User approver', false);
    }

    private function normalizeApproval(mixed $value): string
    {
        return match (TextNormalizer::lower($value)) {
            'menunggu' => 'pending',
            'approved', 'setuju' => 'disetujui',
            'rejected', 'tolak' => 'ditolak',
            default => TextNormalizer::lower($value) ?: 'pending',
        };
    }

    public function rules(): array
    {
        return [
            'guru' => 'required_without:guru_id',
            'jenis_izin' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'nullable|string|max:500',
            'file_surat' => 'nullable|string|max:255',
            'status_approval' => 'nullable|string',
            'disetujui_oleh' => 'nullable',
            'tanggal_approval' => 'nullable|date',
            'catatan_approval' => 'nullable|string|max:500',
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
