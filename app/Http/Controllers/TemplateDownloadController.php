<?php

namespace App\Http\Controllers;

use App\Exports\ImportTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class TemplateDownloadController extends Controller
{
    private function download(string $type)
    {
        $export = new ImportTemplateExport($type);

        return Excel::download($export, $export->filename());
    }

    public function downloadMataPelajaran()
    {
        return $this->download('mata-pelajaran');
    }

    public function downloadSiswa()
    {
        return $this->download('siswa');
    }

    public function downloadGuru()
    {
        return $this->download('guru');
    }

    public function downloadKelas()
    {
        return $this->download('kelas');
    }

    public function downloadJadwal()
    {
        return $this->download('jadwal');
    }

    public function downloadUser()
    {
        return $this->download('user');
    }

    public function downloadIzinGuru()
    {
        return $this->download('izin-guru');
    }

    public function downloadGuruPengganti()
    {
        return $this->download('guru-pengganti');
    }

    public function downloadKehadiran()
    {
        return $this->download('kehadiran');
    }
}
