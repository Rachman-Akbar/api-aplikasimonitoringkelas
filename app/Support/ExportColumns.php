<?php

namespace App\Support;

use pxlrbt\FilamentExcel\Columns\Column;

final class ExportColumns
{
    public static function izinGuru(): array
    {
        return [
            Column::make('guru.nama')->heading('Guru'),
            Column::make('guru.nip')->heading('NIP Guru'),
            Column::make('jenis_izin')->heading('Jenis Izin'),
            Column::make('tanggal_mulai')->heading('Tanggal Mulai'),
            Column::make('tanggal_selesai')->heading('Tanggal Selesai'),
            Column::make('durasi_hari')->heading('Durasi Hari'),
            Column::make('keterangan')->heading('Keterangan'),
            Column::make('status_approval')->heading('Status Approval'),
            Column::make('disetujuiOleh.name')->heading('Disetujui Oleh'),
            Column::make('tanggal_approval')->heading('Tanggal Approval'),
            Column::make('catatan_approval')->heading('Catatan Approval'),
            Column::make('created_at')->heading('Dibuat Pada'),
            Column::make('updated_at')->heading('Diubah Pada'),
        ];
    }

    public static function guruPengganti(): array
    {
        return [
            Column::make('tanggal')->heading('Tanggal'),
            Column::make('jadwal.kelas.nama')->heading('Kelas'),
            Column::make('jadwal.mataPelajaran.nama')->heading('Mata Pelajaran'),
            Column::make('jadwal.hari')->heading('Hari'),
            Column::make('jadwal.jam_ke')->heading('Jam Ke'),
            Column::make('guruAsli.nama')->heading('Guru Asli'),
            Column::make('guruPengganti.nama')->heading('Guru Pengganti'),
            Column::make('status_penggantian')->heading('Status Penggantian'),
            Column::make('keterangan')->heading('Keterangan'),
            Column::make('catatan_approval')->heading('Catatan Approval'),
            Column::make('disetujuiOleh.name')->heading('Disetujui Oleh'),
            Column::make('created_at')->heading('Dibuat Pada'),
            Column::make('updated_at')->heading('Diubah Pada'),
        ];
    }

    public static function kehadiran(): array
    {
        return [
            Column::make('siswa.nama')->heading('Siswa'),
            Column::make('siswa.nis')->heading('NIS'),
            Column::make('siswa.kelas.nama')->heading('Kelas'),
            Column::make('jadwal.mataPelajaran.nama')->heading('Mata Pelajaran'),
            Column::make('jadwal.guru.nama')->heading('Guru'),
            Column::make('jadwal.hari')->heading('Hari'),
            Column::make('jadwal.jam_ke')->heading('Jam Ke'),
            Column::make('tanggal')->heading('Tanggal'),
            Column::make('status')->heading('Status'),
            Column::make('keterangan')->heading('Keterangan'),
            Column::make('diinputOleh.name')->heading('Diinput Oleh'),
            Column::make('created_at')->heading('Dibuat Pada'),
            Column::make('updated_at')->heading('Diubah Pada'),
        ];
    }

    public static function kehadiranGuru(): array
    {
        return [
            Column::make('guru.nama')->heading('Guru'),
            Column::make('guru.nip')->heading('NIP'),
            Column::make('jadwal.kelas.nama')->heading('Kelas'),
            Column::make('jadwal.mataPelajaran.nama')->heading('Mata Pelajaran'),
            Column::make('jadwal.hari')->heading('Hari'),
            Column::make('jadwal.jam_ke')->heading('Jam Ke'),
            Column::make('tanggal')->heading('Tanggal'),
            Column::make('status_kehadiran')->heading('Status Kehadiran'),
            Column::make('waktu_datang')->heading('Waktu Datang'),
            Column::make('durasi_keterlambatan')->heading('Durasi Keterlambatan'),
            Column::make('keterangan')->heading('Keterangan'),
            Column::make('diinputOleh.name')->heading('Diinput Oleh'),
            Column::make('created_at')->heading('Dibuat Pada'),
        ];
    }
}
