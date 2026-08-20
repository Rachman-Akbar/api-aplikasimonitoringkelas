<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\User;
use App\Support\RelationResolver;
use App\Support\TextNormalizer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UserImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $email = TextNormalizer::trim($row['email'] ?? null);
        $record = User::query()->whereRaw('LOWER(TRIM(email)) = ?', [mb_strtolower((string) $email, 'UTF-8')])->first() ?? new User();
        $role = TextNormalizer::lower($row['role'] ?? 'siswa');
        $guruId = $this->resolveOptional(Guru::class, 'nama', $row['guru'] ?? $row['guru_id'] ?? null, 'Guru');
        $kelasId = $this->resolveOptional(Kelas::class, 'nama', $row['kelas'] ?? $row['kelas_id'] ?? null, 'Kelas');

        if ($role === 'guru' && !$guruId) {
            throw ValidationException::withMessages(['guru' => 'Nama guru wajib diisi untuk role guru.']);
        }

        if ($role === 'siswa' && !$kelasId) {
            throw ValidationException::withMessages(['kelas' => 'Nama kelas wajib diisi untuk role siswa.']);
        }

        $record->fill([
            'name' => TextNormalizer::trim($row['name'] ?? null),
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

        return $record;
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

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:admin,kepsek,kurikulum,guru,siswa',
        ];
    }
}
