<?php

namespace App\Imports;

use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class KelasImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Kelas([
            'nama' => $row['nama'],
            'tingkat' => $row['tingkat'],
            'jurusan' => $row['jurusan'],
            'wali_kelas_id' => $row['wali_kelas_id'],
            'kapasitas' => $row['kapasitas'],
            'jumlah_siswa' => $row['jumlah_siswa'],
            'ruangan' => $row['ruangan'],
            'status' => $row['status'],
        ]);
    }

    // For imports, perform normalization/casting inside model() if needed.

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'tingkat' => 'required|integer|min:1|max:12',
            'jurusan' => 'required|string|max:255',
            'wali_kelas_id' => 'nullable|exists:gurus,id',
            'kapasitas' => 'required|integer|min:0',
            'jumlah_siswa' => 'required|integer|min:0',
            'ruangan' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
        ];
    }
}
