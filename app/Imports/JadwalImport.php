<?php

namespace App\Imports;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Guru;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;

class JadwalImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Normalize headers
        $normalized = [];
        foreach ($row as $key => $value) {
            $k = Str::of($key)->trim()->lower()->replace(' ', '_')->__toString();
            $normalized[$k] = is_string($value) ? trim($value) : $value;
        }
        $row = $normalized;

        // Resolve kelas/mata_pelajaran/guru by name if id not provided
        if (empty($row['kelas_id']) && !empty($row['kelas'])) {
            $kelas = Kelas::where('nama', $row['kelas'])->first();
            if ($kelas) $row['kelas_id'] = $kelas->id;
        }
        if (empty($row['mata_pelajaran_id']) && !empty($row['mata_pelajaran'])) {
            $mp = MataPelajaran::where('nama', $row['mata_pelajaran'])->first();
            if ($mp) $row['mata_pelajaran_id'] = $mp->id;
        }
        if (empty($row['guru_id']) && !empty($row['guru'])) {
            $g = Guru::where('nama', $row['guru'])->first();
            if ($g) $row['guru_id'] = $g->id;
        }
        return new Jadwal([
            'kelas_id' => $row['kelas_id'] ?? null,
            'mata_pelajaran_id' => $row['mata_pelajaran_id'] ?? null,
            'guru_id' => $row['guru_id'] ?? null,
            'hari' => $row['hari'] ?? null,
            'jam_ke' => $row['jam_ke'] ?? null,
            'jam_mulai' => $row['jam_mulai'] ?? null,
            'jam_selesai' => $row['jam_selesai'] ?? null,
            'ruangan' => $row['ruangan'] ?? null,
            'status' => $row['status'] ?? 'aktif',
            'keterangan' => $row['keterangan'] ?? null,
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function rules(): array
    {
        return [
            'kelas_id' => 'nullable|exists:kelas,id',
            'mata_pelajaran_id' => 'nullable|exists:mata_pelajarans,id',
            'guru_id' => 'nullable|exists:gurus,id',
            'hari' => 'nullable|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_ke' => 'nullable|integer|min:1',
            'status' => 'nullable|in:aktif,nonaktif',
        ];
    }
}
