<?php

namespace App\Services;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\User;
use App\Support\RelationResolver;
use App\Support\TextNormalizer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ImportService
{
    public function importJadwal(array $data): array
    {
        return $this->process($data, function (array $row): void {
            $kelasId = $this->resolveRequired(Kelas::class, 'nama', $row['kelas'] ?? $row['kelas_id'] ?? null, 'Kelas');
            $mataPelajaranId = $this->resolveRequired(MataPelajaran::class, 'nama', $row['mata_pelajaran'] ?? $row['mata_pelajaran_id'] ?? null, 'Mata pelajaran');
            $guruId = $this->resolveRequired(Guru::class, 'nama', $row['guru'] ?? $row['guru_id'] ?? null, 'Guru');
            $hari = $this->normalizeHari($row['hari'] ?? null);
            $jamKe = (int) ($row['jam_ke'] ?? 0);

            $validator = Validator::make([
                'hari' => $hari,
                'jam_ke' => $jamKe,
                'jam_mulai' => $row['jam_mulai'] ?? null,
                'jam_selesai' => $row['jam_selesai'] ?? null,
                'status' => TextNormalizer::lower($row['status'] ?? 'aktif'),
            ], [
                'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
                'jam_ke' => 'required|integer|min:1|max:15',
                'jam_mulai' => 'required',
                'jam_selesai' => 'required',
                'status' => 'required|in:aktif,nonaktif,libur,dibatalkan',
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages($validator->errors()->toArray());
            }

            $record = Jadwal::firstOrNew([
                'kelas_id' => $kelasId,
                'mata_pelajaran_id' => $mataPelajaranId,
                'guru_id' => $guruId,
                'hari' => $hari,
                'jam_ke' => $jamKe,
            ]);

            $record->fill([
                'jam_mulai' => $row['jam_mulai'] ?? null,
                'jam_selesai' => $row['jam_selesai'] ?? null,
                'tahun_ajaran' => TextNormalizer::trim($row['tahun_ajaran'] ?? null),
                'ruangan' => TextNormalizer::lower($row['ruangan'] ?? null),
                'status' => TextNormalizer::lower($row['status'] ?? 'aktif'),
                'keterangan' => TextNormalizer::trim($row['keterangan'] ?? null),
            ])->save();
        });
    }

    public function importSiswa(array $data): array
    {
        return $this->process($data, function (array $row): void {
            $validator = Validator::make($row, [
                'nis' => 'required|string|max:20',
                'nisn' => 'required|string|max:20',
                'nama' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'jenis_kelamin' => 'required|string',
                'tanggal_lahir' => 'nullable|date',
                'status' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages($validator->errors()->toArray());
            }

            $kelasId = $this->resolveRequired(Kelas::class, 'nama', $row['kelas'] ?? $row['kelas_id'] ?? null, 'Kelas');
            $nis = TextNormalizer::trim($row['nis']);
            $nisn = TextNormalizer::trim($row['nisn']);
            $record = Siswa::query()->whereRaw('LOWER(TRIM(nis)) = ?', [mb_strtolower((string) $nis, 'UTF-8')])->first();

            if (!$record) {
                $record = Siswa::query()->whereRaw('LOWER(TRIM(nisn)) = ?', [mb_strtolower((string) $nisn, 'UTF-8')])->first();
            }

            $record ??= new Siswa();
            $record->fill([
                'nis' => $nis,
                'nisn' => $nisn,
                'nama' => TextNormalizer::trim($row['nama']),
                'email' => TextNormalizer::trim($row['email'] ?? null),
                'no_telp' => TextNormalizer::trim($row['no_telp'] ?? null),
                'alamat' => TextNormalizer::trim($row['alamat'] ?? null),
                'jenis_kelamin' => $this->normalizeGender($row['jenis_kelamin']),
                'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
                'kelas_id' => $kelasId,
                'nama_orang_tua' => TextNormalizer::trim($row['nama_orang_tua'] ?? null),
                'no_telp_orang_tua' => TextNormalizer::trim($row['no_telp_orang_tua'] ?? null),
                'status' => TextNormalizer::lower($row['status'] ?? 'aktif'),
            ])->save();
        });
    }

    public function importGuru(array $data): array
    {
        return $this->process($data, function (array $row): void {
            $validator = Validator::make($row, [
                'nip' => 'required|string|max:20',
                'nama' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'jenis_kelamin' => 'required|string',
                'tanggal_lahir' => 'nullable|date',
                'status' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages($validator->errors()->toArray());
            }

            $nip = TextNormalizer::trim($row['nip']);
            $record = Guru::query()->whereRaw('LOWER(TRIM(nip)) = ?', [mb_strtolower((string) $nip, 'UTF-8')])->first();

            if (!$record && !empty($row['email'])) {
                $record = Guru::query()->whereRaw('LOWER(TRIM(email)) = ?', [mb_strtolower((string) TextNormalizer::trim($row['email']), 'UTF-8')])->first();
            }

            $record ??= new Guru();
            $record->fill([
                'nip' => $nip,
                'nama' => TextNormalizer::trim($row['nama']),
                'email' => TextNormalizer::trim($row['email']),
                'no_telp' => TextNormalizer::trim($row['no_telp'] ?? null),
                'alamat' => TextNormalizer::trim($row['alamat'] ?? null),
                'jenis_kelamin' => $this->normalizeGender($row['jenis_kelamin']),
                'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
                'status' => TextNormalizer::lower($row['status'] ?? 'aktif'),
            ])->save();
        });
    }

    public function importMataPelajaran(array $data): array
    {
        return $this->process($data, function (array $row): void {
            $validator = Validator::make($row, [
                'kode' => 'required|string|max:20',
                'nama' => 'required|string|max:100',
                'deskripsi' => 'nullable|string|max:255',
                'sks' => 'nullable|integer|min:0|max:10',
                'kategori' => 'nullable|string|max:50',
                'status' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages($validator->errors()->toArray());
            }

            $kode = TextNormalizer::lower($row['kode']);
            $nama = TextNormalizer::lower($row['nama']);
            $record = MataPelajaran::query()->whereRaw('LOWER(TRIM(kode)) = ?', [$kode])->first();

            if (!$record) {
                $record = MataPelajaran::query()->whereRaw('LOWER(TRIM(nama)) = ?', [$nama])->first();
            }

            $record ??= new MataPelajaran();
            $record->fill([
                'kode' => $kode,
                'nama' => $nama,
                'deskripsi' => TextNormalizer::trim($row['deskripsi'] ?? null),
                'sks' => $row['sks'] ?? 1,
                'kategori' => TextNormalizer::lower($row['kategori'] ?? null),
                'status' => TextNormalizer::lower($row['status'] ?? 'aktif'),
            ])->save();
        });
    }

    public function importUser(array $data): array
    {
        return $this->process($data, function (array $row): void {
            $validator = Validator::make($row, [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'password' => 'nullable|string|min:8',
                'role' => 'required|in:admin,kepsek,kurikulum,guru,siswa',
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages($validator->errors()->toArray());
            }

            $email = TextNormalizer::trim($row['email']);
            $record = User::query()->whereRaw('LOWER(TRIM(email)) = ?', [mb_strtolower((string) $email, 'UTF-8')])->first() ?? new User();
            $role = TextNormalizer::lower($row['role']);
            $guruId = $this->resolveOptional(Guru::class, 'nama', $row['guru'] ?? $row['guru_id'] ?? null, 'Guru');
            $kelasId = $this->resolveOptional(Kelas::class, 'nama', $row['kelas'] ?? $row['kelas_id'] ?? null, 'Kelas');

            if ($role === 'guru' && !$guruId) {
                throw ValidationException::withMessages(['guru' => 'Nama guru wajib diisi untuk role guru.']);
            }

            if ($role === 'siswa' && !$kelasId) {
                throw ValidationException::withMessages(['kelas' => 'Nama kelas wajib diisi untuk role siswa.']);
            }

            $record->fill([
                'name' => TextNormalizer::trim($row['name']),
                'email' => $email,
                'role' => $role,
                'guru_id' => $guruId,
                'kelas_id' => $kelasId,
            ]);

            if (!empty($row['password'])) {
                $record->password = Hash::make((string) $row['password']);
            } elseif (!$record->exists) {
                throw ValidationException::withMessages(['password' => 'Password wajib diisi untuk user baru.']);
            }

            $record->save();
        });
    }

    private function process(array $data, callable $handler): array
    {
        $success = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                $handler($row);
                $success++;
            } catch (\Throwable $e) {
                $errors[] = 'Baris ' . ($index + 1) . ': ' . $e->getMessage();
            }
        }

        return [
            'success' => $success,
            'errors' => count($errors),
            'error_details' => $errors,
        ];
    }

    private function resolveRequired(string $modelClass, string $column, mixed $value, string $label): int
    {
        if (is_numeric($value) && $modelClass::query()->whereKey((int) $value)->exists()) {
            return (int) $value;
        }

        return RelationResolver::idByText($modelClass, $column, $value, $label);
    }

    private function resolveOptional(string $modelClass, string $column, mixed $value, string $label): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value) && $modelClass::query()->whereKey((int) $value)->exists()) {
            return (int) $value;
        }

        return RelationResolver::idByText($modelClass, $column, $value, $label, false);
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

    private function normalizeGender(mixed $value): string
    {
        return match (TextNormalizer::lower($value)) {
            'l', 'laki-laki', 'laki laki', 'male', 'm' => 'L',
            'p', 'perempuan', 'female', 'f' => 'P',
            default => strtoupper((string) TextNormalizer::trim($value)),
        };
    }
}
