<?php

// This is a sample template for testing the import functionality
// It will be used in the documentation to help users understand the structure

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateImportTemplate extends Command
{
    protected $signature = 'import:create-template {type}';
    protected $description = 'Create an import template file for the specified type';

    public function handle()
    {
        $type = $this->argument('type');
        
        if ($type === 'siswa') {
            $this->createSiswaTemplate();
        } elseif ($type === 'guru') {
            $this->createGuruTemplate();
        } elseif ($type === 'kelas') {
            $this->createKelasTemplate();
        } elseif ($type === 'mata-pelajaran') {
            $this->createMataPelajaranTemplate();
        } elseif ($type === 'user') {
            $this->createUserTemplate();
        } else {
            $this->error('Invalid type. Available types: siswa, guru, kelas, mata-pelajaran, user');
            return;
        }
        
        $this->info("Template file created successfully!");
    }

    private function createSiswaTemplate()
    {
        $template = "nis,nisn,nama,email,no_telp,alamat,jenis_kelamin,tanggal_lahir,kelas_id,nama_orang_tua,no_telp_orang_tua,status\n";
        $template .= "12345,987654321,Ahmad Subarkah,ahmad@example.com,08123456789,\"Jl. Merdeka No. 1\",L,2008-05-15,1,Bapak Joko,08123456789,aktif\n";
        $template .= "12346,987654322,Putri Lestari,putri@example.com,08123456790,\"Jl. Sudirman No. 2\",P,2008-07-20,1,Bu Sari,08123456790,aktif\n";
        
        file_put_contents(storage_path('app/templates/siswa_template.csv'), $template);
    }

    private function createGuruTemplate()
    {
        $template = "nip,nama,email,no_telp,alamat,jenis_kelamin,tanggal_lahir,status\n";
        $template .= "197001012005011001,Budi Santoso,budi@example.com,08123456789,\"Jl. Pendidikan No. 10\",L,1970-01-01,aktif\n";
        $template .= "197502052006022002,Siti Rahayu,siti@example.com,08123456790,\"Jl. Guru No. 5\",P,1975-02-05,aktif\n";
        
        file_put_contents(storage_path('app/templates/guru_template.csv'), $template);
    }

    private function createKelasTemplate()
    {
        $template = "nama,tingkat,jurusan,wali_kelas_id,kapasitas,jumlah_siswa,ruangan,status\n";
        $template .= "XII IPA 1,12,IPA,1,30,28,Ruang 101,aktif\n";
        $template .= "XII IPS 1,12,IPS,2,30,25,Ruang 102,aktif\n";
        
        file_put_contents(storage_path('app/templates/kelas_template.csv'), $template);
    }

    private function createMataPelajaranTemplate()
    {
        $template = "kode,nama,deskripsi,sks,kategori,status\n";
        $template .= "MAT001,Matematika,\"Pelajaran Matematika Wajib\",3,wajib,1\n";
        $template .= "IND001,Bahasa Indonesia,\"Pelajaran Bahasa Indonesia Wajib\",3,wajib,1\n";
        
        file_put_contents(storage_path('app/templates/mata_pelajaran_template.csv'), $template);
    }

    private function createUserTemplate()
    {
        $template = "name,email,password,role\n";
        $template .= "Admin Utama,admin@example.com,password,admin\n";
        $template .= "Guru Pengajar,guru@example.com,password,guru\n";
        
        file_put_contents(storage_path('app/templates/user_template.csv'), $template);
    }
}