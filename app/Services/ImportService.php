<?php

namespace App\Services;

use App\Models\Jadwal;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ImportService
{
    /**
     * Import data jadwal dari array
     *
     * @param array $data
     * @return array
     */
    public function importJadwal(array $data): array
    {
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                // Validasi data
                $validator = Validator::make($row, [
                    'kelas_id' => 'required|exists:kelas,id',
                    'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
                    'guru_id' => 'required|exists:gurus,id',
                    'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
                    'jam_ke' => 'required|integer|min:1|max:10',
                    'jam_mulai' => 'required|date_format:H:i',
                    'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
                    'ruangan' => 'nullable|string|max:20',
                    'status' => 'nullable|in:aktif,nonaktif,batal',
                    'keterangan' => 'nullable|string|max:255',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Baris " . ($index + 1) . ": " . $validator->errors()->first();
                    $errorCount++;
                    continue;
                }

                // Cek duplikat
                $duplicate = Jadwal::where('kelas_id', $row['kelas_id'])
                    ->where('mata_pelajaran_id', $row['mata_pelajaran_id'])
                    ->where('guru_id', $row['guru_id'])
                    ->where('hari', $row['hari'])
                    ->where('jam_ke', $row['jam_ke'])
                    ->first();

                if ($duplicate) {
                    Log::info('Jadwal duplikat ditemukan dan dilewati', ['data' => $row]);
                    continue;
                }

                // Simpan data
                Jadwal::create($row);
                $successCount++;
                
                Log::info('Jadwal berhasil diimport', ['data' => $row]);
            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
                Log::error('Error import jadwal', [
                    'row' => $row,
                    'error' => $e->getMessage()
                ]);
            }

            // Untuk mencegah timeout, kita juga bisa menambahkan sleep di sini
            if (($index + 1) % 50 === 0) {
                usleep(100000); // 0.1 detik
            }
        }

        return [
            'success' => $successCount,
            'errors' => $errorCount,
            'error_details' => $errors
        ];
    }

    /**
     * Import data siswa dari array
     *
     * @param array $data
     * @return array
     */
    public function importSiswa(array $data): array
    {
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                // Validasi data
                $validator = Validator::make($row, [
                    'nis' => 'required|string|max:20|unique:siswas,nis',
                    'nisn' => 'required|string|max:20|unique:siswas,nisn',
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:siswas,email',
                    'kelas_id' => 'required|exists:kelas,id',
                    'no_telp' => 'nullable|string|max:20',
                    'alamat' => 'nullable|string|max:500',
                    'jenis_kelamin' => 'nullable|in:L,P',
                    'tanggal_lahir' => 'nullable|date',
                    'nama_orang_tua' => 'nullable|string|max:255',
                    'no_telp_orang_tua' => 'nullable|string|max:20',
                    'status' => 'nullable|in:aktif,nonaktif,lulus,pindah',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Baris " . ($index + 1) . ": " . $validator->errors()->first();
                    $errorCount++;
                    continue;
                }

                // Cek duplikat (berdasarkan NIS, NISN, atau email)
                $duplicate = Siswa::where('nis', $row['nis'])
                    ->orWhere('nisn', $row['nisn'])
                    ->orWhere('email', $row['email'])
                    ->first();

                if ($duplicate) {
                    Log::info('Siswa duplikat ditemukan dan dilewati', ['data' => $row]);
                    continue;
                }

                // Simpan data
                Siswa::create($row);
                $successCount++;
                
                Log::info('Siswa berhasil diimport', ['data' => $row]);
            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
                Log::error('Error import siswa', [
                    'row' => $row,
                    'error' => $e->getMessage()
                ]);
            }

            // Untuk mencegah timeout, kita juga bisa menambahkan sleep di sini
            if (($index + 1) % 50 === 0) {
                usleep(100000); // 0.1 detik
            }
        }

        return [
            'success' => $successCount,
            'errors' => $errorCount,
            'error_details' => $errors
        ];
    }

    /**
     * Import data guru dari array
     *
     * @param array $data
     * @return array
     */
    public function importGuru(array $data): array
    {
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                // Validasi data
                $validator = Validator::make($row, [
                    'nip' => 'required|string|max:20|unique:gurus,nip',
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:gurus,email',
                    'no_telp' => 'nullable|string|max:20',
                    'alamat' => 'nullable|string|max:500',
                    'jenis_kelamin' => 'nullable|in:L,P',
                    'tanggal_lahir' => 'nullable|date',
                    'status' => 'nullable|in:aktif,nonaktif',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Baris " . ($index + 1) . ": " . $validator->errors()->first();
                    $errorCount++;
                    continue;
                }

                // Cek duplikat
                $duplicate = Guru::where('nip', $row['nip'])
                    ->orWhere('email', $row['email'])
                    ->first();

                if ($duplicate) {
                    Log::info('Guru duplikat ditemukan dan dilewati', ['data' => $row]);
                    continue;
                }

                // Simpan data
                Guru::create($row);
                $successCount++;
                
                Log::info('Guru berhasil diimport', ['data' => $row]);
            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
                Log::error('Error import guru', [
                    'row' => $row,
                    'error' => $e->getMessage()
                ]);
            }

            // Untuk mencegah timeout, kita juga bisa menambahkan sleep di sini
            if (($index + 1) % 50 === 0) {
                usleep(100000); // 0.1 detik
            }
        }

        return [
            'success' => $successCount,
            'errors' => $errorCount,
            'error_details' => $errors
        ];
    }

    /**
     * Import data mata pelajaran dari array
     *
     * @param array $data
     * @return array
     */
    public function importMataPelajaran(array $data): array
    {
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                // Validasi data
                $validator = Validator::make($row, [
                    'kode' => 'required|string|max:20|unique:mata_pelajarans,kode',
                    'nama' => 'required|string|max:100',
                    'deskripsi' => 'nullable|string|max:255',
                    'sks' => 'nullable|integer|min:1|max:10',
                    'kategori' => 'nullable|in:wajib,pilihan,muatan-lokal',
                    'status' => 'nullable|in:aktif,nonaktif',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Baris " . ($index + 1) . ": " . $validator->errors()->first();
                    $errorCount++;
                    continue;
                }

                // Cek duplikat
                $duplicate = MataPelajaran::where('kode', $row['kode'])->first();

                if ($duplicate) {
                    Log::info('Mata pelajaran duplikat ditemukan dan dilewati', ['data' => $row]);
                    continue;
                }

                // Simpan data
                MataPelajaran::create($row);
                $successCount++;
                
                Log::info('Mata pelajaran berhasil diimport', ['data' => $row]);
            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
                Log::error('Error import mata pelajaran', [
                    'row' => $row,
                    'error' => $e->getMessage()
                ]);
            }

            // Untuk mencegah timeout, kita juga bisa menambahkan sleep di sini
            if (($index + 1) % 50 === 0) {
                usleep(100000); // 0.1 detik
            }
        }

        return [
            'success' => $successCount,
            'errors' => $errorCount,
            'error_details' => $errors
        ];
    }

    /**
     * Import data user dari array
     *
     * @param array $data
     * @return array
     */
    public function importUser(array $data): array
    {
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                // Validasi data
                $validator = Validator::make($row, [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:users,email',
                    'password' => 'nullable|string|min:8',
                    'role' => 'required|in:admin,kepsek,kurikulum,guru,siswa',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Baris " . ($index + 1) . ": " . $validator->errors()->first();
                    $errorCount++;
                    continue;
                }

                // Set password default jika tidak disediakan
                if (empty($row['password'])) {
                    $row['password'] = bcrypt('password123');
                } else {
                    $row['password'] = bcrypt($row['password']);
                }

                // Cek duplikat
                $duplicate = User::where('email', $row['email'])->first();

                if ($duplicate) {
                    Log::info('User duplikat ditemukan dan dilewati', ['data' => $row]);
                    continue;
                }

                // Simpan data
                User::create($row);
                $successCount++;
                
                Log::info('User berhasil diimport', ['data' => $row]);
            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
                Log::error('Error import user', [
                    'row' => $row,
                    'error' => $e->getMessage()
                ]);
            }

            // Untuk mencegah timeout, kita juga bisa menambahkan sleep di sini
            if (($index + 1) % 50 === 0) {
                usleep(100000); // 0.1 detik
            }
        }

        return [
            'success' => $successCount,
            'errors' => $errorCount,
            'error_details' => $errors
        ];
    }
}