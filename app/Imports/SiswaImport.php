<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $row = $this->normalizeRow($row);

        // If kelas provided as name, try to resolve to id
        if (empty($row['kelas_id']) && !empty($row['kelas'])) {
            $kelas = Kelas::where('nama', $row['kelas'])->first();
            if ($kelas) {
                $row['kelas_id'] = $kelas->id;
            }
        }

        // Normalize jenis_kelamin values
        if (!empty($row['jenis_kelamin'])) {
            $jk = Str::upper(trim($row['jenis_kelamin']));
            if (in_array($jk, ['LAKI-LAKI', 'L', 'LAKI-LAKI', 'LAKI'], true)) {
                $row['jenis_kelamin'] = 'L';
            } elseif (in_array($jk, ['PEREMPUAN', 'P', 'PEREMPUAN'], true)) {
                $row['jenis_kelamin'] = 'P';
            }
        }

        // Parse tanggal_lahir if present and not null
        if (!empty($row['tanggal_lahir'])) {
            try {
                $dt = Carbon::parse($row['tanggal_lahir']);
                $row['tanggal_lahir'] = $dt->toDateString();
            } catch (\Throwable $e) {
                // leave as-is; validation may catch invalid date
            }
        }
        return new Siswa([
            'nis' => $row['nis'] ?? null,
            'nisn' => $row['nisn'] ?? null,
            'nama' => $row['nama'] ?? null,
            'email' => $row['email'] ?? null,
            'no_telp' => $row['no_telp'] ?? null,
            'alamat' => $row['alamat'] ?? null,
            'jenis_kelamin' => $row['jenis_kelamin'] ?? null,
            'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
            'foto' => $row['foto'] ?? null,
            'kelas_id' => $row['kelas_id'] ?? null,
            'nama_orang_tua' => $row['nama_orang_tua'] ?? null,
            'no_telp_orang_tua' => $row['no_telp_orang_tua'] ?? null,
            'status' => $row['status'] ?? 'aktif',
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
            'nis' => 'required|string|max:255|unique:siswas,nis',
            'nisn' => 'nullable|string|max:255|unique:siswas,nisn',
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:siswas,email',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'kelas_id' => 'nullable|exists:kelas,id',
            'status' => 'nullable|in:aktif,nonaktif,lulus,pindah',
        ];
    }

    /**
     * Normalize heading keys: lowercase, trim and replace spaces with underscores.
     */
    private function normalizeRow(array $row): array
    {
        $normalized = [];
        foreach ($row as $key => $value) {
            $k = Str::of($key)->trim()->lower()->replace(' ', '_')->__toString();
            $normalized[$k] = is_string($value) ? trim($value) : $value;
        }

        // Merge normalized back so we can accept alternative headers like 'kelas' or 'kelas_nama'
        return $normalized;
    }
}
