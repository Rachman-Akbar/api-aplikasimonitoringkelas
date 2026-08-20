<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class TemplateDownloadController extends Controller
{
    public function downloadMataPelajaran()
    {
        $filePath = base_path('import_template/sample_mata_pelajaran.csv');

        if (file_exists($filePath)) {
            return Response::download($filePath, 'template_mata_pelajaran.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        // If the CSV template doesn't exist, create it dynamically
        $headers = ['id', 'kode', 'nama', 'deskripsi', 'sks', 'kategori', 'status'];
        $sampleData = [
            ['', 'MTK001', 'Matematika', 'Pelajaran matematika dasar', '3', 'Keahlian', 'aktif'],
            ['1', 'IND001', 'Bahasa Indonesia', 'Bahasa dan Sastra Indonesia', '2', 'Normatif', 'aktif'],
            ['2', 'ING001', 'Bahasa Inggris', 'Bahasa Inggris untuk komunikasi', '2', 'Normatif', 'aktif'],
        ];

        $csv = implode(',', $headers) . "\n";
        foreach ($sampleData as $row) {
            $csv .= '"' . implode('","', $row) . '"' . "\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_mata_pelajaran.csv"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function downloadSiswa()
    {
        $filePath = base_path('import_template/sample_siswa.csv');

        if (file_exists($filePath)) {
            return Response::download($filePath, 'template_siswa.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        // If the CSV template doesn't exist, create it dynamically
        $headers = ['id', 'nis', 'nisn', 'nama', 'email', 'no_telp', 'kelas_id', 'alamat', 'jenis_kelamin', 'tanggal_lahir', 'nama_orang_tua', 'no_telp_orang_tua', 'status'];
        $sampleData = [
            ['', '2024001', '0012345678', 'Ahmad Fauzi', 'ahmad.fauzi.siswa@example.com', '081234567890', '1', 'Jl. Merdeka No. 10 Jakarta Selatan', 'L', '2008-01-01', 'Budi Santoso', '081345678901', 'aktif'],
            ['1', '2024002', '0012345679', 'Siti Nurjanah', 'siti.nurjanah.siswa@example.com', '082345678901', '1', 'Jl. Pahlawan No. 15 Jakarta Timur', 'P', '2007-05-15', 'Agus Handoko', '082456789012', 'aktif'],
            ['2', '2024003', '0012345680', 'Rizki Ramadhan', 'rizki.ramadhan.siswa@example.com', '083456789012', '2', 'Jl. Sudirman No. 20 Jakarta Pusat', 'L', '2008-03-20', 'Dewi Lestari', '083567890123', 'aktif'],
        ];

        $csv = implode(',', $headers) . "\n";
        foreach ($sampleData as $row) {
            // Properly escape fields that contain commas, quotes, or newlines
            $escapedRow = array_map(function ($field) {
                if (strpos($field, ',') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false) {
                    return '"' . str_replace('"', '""', $field) . '"';
                }
                return $field;
            }, $row);
            $csv .= implode(',', $escapedRow) . "\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_siswa.csv"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function downloadGuru()
    {
        $filePath = base_path('import_template/sample_guru.csv');

        if (file_exists($filePath)) {
            return Response::download($filePath, 'template_guru.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        // If the CSV template doesn't exist, create it dynamically
        $headers = ['id', 'nip', 'nama', 'email', 'no_telp', 'alamat', 'jenis_kelamin', 'tanggal_lahir', 'status'];
        $sampleData = [
            ['', '19850101001', 'Ahmad Basuki', 'ahmad.basuki@example.com', '081234567890', 'Jl. Pendidikan No. 1', 'L', '1985-01-01', 'aktif'],
            ['1', '19900515002', 'Siti Aminah', 'siti.aminah@example.com', '082345678901', 'Jl. Guru No. 15', 'P', '1990-05-15', 'aktif'],
        ];

        $csv = implode(',', $headers) . "\n";
        foreach ($sampleData as $row) {
            $escapedRow = array_map(function ($field) {
                if (strpos($field, ',') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false) {
                    return '"' . str_replace('"', '""', $field) . '"';
                }
                return $field;
            }, $row);
            $csv .= implode(',', $escapedRow) . "\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_guru.csv"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function downloadKelas()
    {
        $filePath = base_path('import_template/sample_kelas.csv');

        if (file_exists($filePath)) {
            return Response::download($filePath, 'template_kelas.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        // If the CSV template doesn't exist, create it dynamically
        $headers = ['id', 'nama', 'tingkat', 'jurusan', 'tahun_ajaran', 'wali_kelas_id', 'kapasitas', 'status'];
        $sampleData = [
            ['', 'X RPL 1', '10', 'RPL', '2024/2025', '1', '36', 'aktif'],
            ['1', 'X RPL 2', '10', 'RPL', '2024/2025', '2', '36', 'aktif'],
            ['2', 'XI RPL 1', '11', 'RPL', '2024/2025', '3', '32', 'aktif'],
        ];

        $csv = implode(',', $headers) . "\n";
        foreach ($sampleData as $row) {
            $escapedRow = array_map(function ($field) {
                if (strpos($field, ',') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false) {
                    return '"' . str_replace('"', '""', $field) . '"';
                }
                return $field;
            }, $row);
            $csv .= implode(',', $escapedRow) . "\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_kelas.csv"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function downloadJadwal()
    {
        $filePath = base_path('import_template/sample_jadwal.csv');

        if (file_exists($filePath)) {
            return Response::download($filePath, 'template_jadwal.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        // If the CSV template doesn't exist, create it dynamically
        $headers = ['id', 'hari', 'jam_mulai', 'jam_selesai', 'kelas_id', 'mata_pelajaran_id', 'guru_id', 'ruangan', 'semester', 'tahun_ajaran'];
        $sampleData = [
            ['', 'Senin', '07:00', '08:30', '1', '1', '1', 'Lab Komputer 1', 'Ganjil', '2024/2025'],
            ['1', 'Senin', '08:30', '10:00', '1', '2', '2', 'Ruang 101', 'Ganjil', '2024/2025'],
            ['2', 'Selasa', '07:00', '08:30', '2', '1', '1', 'Lab Komputer 2', 'Ganjil', '2024/2025'],
        ];

        $csv = implode(',', $headers) . "\n";
        foreach ($sampleData as $row) {
            $csv .= '"' . implode('","', $row) . '"' . "\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_jadwal.csv"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function downloadUser()
    {
        $filePath = base_path('import_template/sample_user.csv');

        if (file_exists($filePath)) {
            return Response::download($filePath, 'template_user.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        // If the CSV template doesn't exist, create it dynamically
        $headers = ['id', 'name', 'email', 'password', 'role'];
        $sampleData = [
            ['', 'Admin User', 'admin@example.com', 'password123', 'admin'],
            ['1', 'Guru User', 'guru@example.com', 'password123', 'guru'],
            ['2', 'Siswa User', 'siswa@example.com', 'password123', 'siswa'],
        ];

        $csv = implode(',', $headers) . "\n";
        foreach ($sampleData as $row) {
            $csv .= '"' . implode('","', $row) . '"' . "\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_user.csv"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function downloadIzinGuru()
    {
        $filePath = base_path('import_template/sample_izin_guru.csv');

        if (file_exists($filePath)) {
            return Response::download($filePath, 'template_izin_guru.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        // If the CSV template doesn't exist, create it dynamically
        $headers = ['id', 'guru_id', 'jenis_izin', 'tanggal_mulai', 'tanggal_selesai', 'alasan', 'file_surat', 'status_approval', 'disetujui_oleh', 'tanggal_approval', 'catatan_approval'];
        $sampleData = [
            ['', '1', 'sakit', '2025-01-15', '2025-01-17', 'Sakit demam tinggi', '/storage/surat/izin_guru_001.pdf', 'disetujui', '2', '2025-01-14 10:30:00', 'Semoga cepat sembuh'],
            ['1', '2', 'izin', '2025-02-20', '2025-02-20', 'Keperluan keluarga', '/storage/surat/izin_guru_002.pdf', 'menunggu', '', '', ''],
            ['2', '3', 'cuti', '2025-03-10', '2025-03-14', 'Cuti tahunan', '/storage/surat/izin_guru_003.pdf', 'disetujui', '2', '2025-03-01 14:15:00', 'Disetujui sesuai jadwal cuti'],
        ];

        $csv = implode(',', $headers) . "\n";
        foreach ($sampleData as $row) {
            $escapedRow = array_map(function ($field) {
                if (strpos($field, ',') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false) {
                    return '"' . str_replace('"', '""', $field) . '"';
                }
                return $field;
            }, $row);
            $csv .= implode(',', $escapedRow) . "\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_izin_guru.csv"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function downloadGuruPengganti()
    {
        $filePath = base_path('import_template/sample_guru_pengganti.csv');

        if (file_exists($filePath)) {
            return Response::download($filePath, 'template_guru_pengganti.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        // If the CSV template doesn't exist, create it dynamically
        $headers = ['id', 'guru_mengajar_id', 'guru_pengganti_id', 'alasan_penggantian', 'status_penggantian', 'catatan', 'dibuat_oleh'];
        $sampleData = [
            ['', '1', '2', 'Guru utama sakit', 'terlaksana', 'Penggantian berjalan lancar', '3'],
            ['1', '2', '3', 'Guru utama izin', 'dijadwalkan', 'Sudah konfirmasi dengan guru pengganti', '3'],
            ['2', '3', '4', 'Guru utama cuti', 'terlaksana', 'Materi sudah disampaikan lengkap', '3'],
        ];

        $csv = implode(',', $headers) . "\n";
        foreach ($sampleData as $row) {
            $escapedRow = array_map(function ($field) {
                if (strpos($field, ',') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false) {
                    return '"' . str_replace('"', '""', $field) . '"';
                }
                return $field;
            }, $row);
            $csv .= implode(',', $escapedRow) . "\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_guru_pengganti.csv"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function downloadKehadiran()
    {
        $filePath = base_path('import_template/sample_kehadiran.csv');

        if (file_exists($filePath)) {
            return Response::download($filePath, 'template_kehadiran.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        // If the CSV template doesn't exist, create it dynamically
        $headers = ['id', 'siswa_id', 'jadwal_id', 'tanggal', 'status_kehadiran', 'keterangan'];
        $sampleData = [
            ['', '1', '1', '2025-01-15', 'hadir', ''],
            ['1', '2', '1', '2025-01-15', 'hadir', ''],
            ['2', '3', '2', '2025-01-15', 'sakit', 'Sakit flu'],
        ];

        $csv = implode(',', $headers) . "\n";
        foreach ($sampleData as $row) {
            $escapedRow = array_map(function ($field) {
                if (strpos($field, ',') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false) {
                    return '"' . str_replace('"', '""', $field) . '"';
                }
                return $field;
            }, $row);
            $csv .= implode(',', $escapedRow) . "\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_kehadiran.csv"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
