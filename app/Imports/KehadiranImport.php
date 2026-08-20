<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kehadiran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\User;
use App\Support\RelationResolver;
use App\Support\TextNormalizer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class KehadiranImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    public function model(array $row)
    {
        $siswaValue = $row['siswa'] ?? $row['siswa_id'] ?? null;
        $siswaId = $this->resolveId(Siswa::class, 'nama', $siswaValue, 'Siswa');
        $jadwalId = $this->resolveJadwal($row);
        $inputBy = $row['diinput_oleh'] ?? null;

        $record = Kehadiran::firstOrNew([
            'siswa_id' => $siswaId,
            'jadwal_id' => $jadwalId,
            'tanggal' => $row['tanggal'] ?? null,
        ]);

        $record->fill([
            'status' => TextNormalizer::lower($row['status'] ?? 'hadir'),
            'keterangan' => TextNormalizer::trim($row['keterangan'] ?? null),
            'diinput_oleh' => $this->resolveOptionalUser($inputBy),
        ]);

        return $record;
    }

    private function resolveJadwal(array $row): int
    {
        if (!empty($row['jadwal_id']) && is_numeric($row['jadwal_id']) && Jadwal::query()->whereKey((int) $row['jadwal_id'])->exists()) {
            return (int) $row['jadwal_id'];
        }

        $kelasId = $this->resolveId(Kelas::class, 'nama', $row['kelas'] ?? null, 'Kelas');
        $mapelId = $this->resolveId(MataPelajaran::class, 'nama', $row['mata_pelajaran'] ?? null, 'Mata pelajaran');
        $guruId = $this->resolveId(Guru::class, 'nama', $row['guru'] ?? null, 'Guru');
        $hari = $this->normalizeHari($row['hari'] ?? null);
        $jamKe = (int) ($row['jam_ke'] ?? 0);
        $jadwal = Jadwal::query()
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $guruId)
            ->where('hari', $hari)
            ->where('jam_ke', $jamKe)
            ->first();

        if (!$jadwal) {
            throw \Illuminate\Validation\ValidationException::withMessages(['jadwal' => 'Jadwal tidak ditemukan dari kombinasi kelas, mata pelajaran, guru, hari, dan jam_ke.']);
        }

        return $jadwal->id;
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

        return RelationResolver::idByText(User::class, 'name', $value, 'User penginput', false);
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

    public function rules(): array
    {
        return [
            'siswa' => 'required_without:siswa_id',
            'tanggal' => 'required|date',
            'status' => 'required|string',
            'keterangan' => 'nullable|string|max:500',
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
