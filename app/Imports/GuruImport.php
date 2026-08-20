<?php

namespace App\Imports;

use App\Models\Guru;
use App\Support\TextNormalizer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class GuruImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $nip = TextNormalizer::trim($row['nip'] ?? null);
        $record = Guru::query()->whereRaw('LOWER(TRIM(nip)) = ?', [mb_strtolower((string) $nip, 'UTF-8')])->first() ?? new Guru();
        $record->fill([
            'nip' => $nip,
            'nama' => TextNormalizer::trim($row['nama'] ?? null),
            'email' => TextNormalizer::trim($row['email'] ?? null),
            'no_telp' => TextNormalizer::trim($row['no_telp'] ?? null),
            'alamat' => TextNormalizer::trim($row['alamat'] ?? null),
            'jenis_kelamin' => strtoupper((string) TextNormalizer::trim($row['jenis_kelamin'] ?? null)),
            'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
            'status' => TextNormalizer::lower($row['status'] ?? 'aktif'),
        ]);

        return $record;
    }

    public function rules(): array
    {
        return [
            'nip' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'jenis_kelamin' => 'required|in:L,P,l,p',
            'tanggal_lahir' => 'nullable|date',
            'status' => 'nullable|string',
        ];
    }
}
