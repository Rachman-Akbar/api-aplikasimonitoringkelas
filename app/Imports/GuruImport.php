<?php

namespace App\Imports;

use App\Models\Guru;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class GuruImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Guru([
            'nip' => $row['nip'],
            'nama' => $row['nama'],
            'email' => $row['email'],
            'no_telp' => $row['no_telp'],
            'alamat' => $row['alamat'],
            'jenis_kelamin' => $row['jenis_kelamin'],
            'tanggal_lahir' => $row['tanggal_lahir'],
            'status' => $row['status'],
        ]);
    }

    // Normalize/cast values inside model() if needed. WithHeadingRow will
    // give you associative rows keyed by the header names.

    public function rules(): array
    {
        return [
            'nip' => 'required|string|unique:gurus,nip|max:255',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:gurus,email|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date',
            'status' => 'required|in:aktif,nonaktif',
        ];
    }
}
