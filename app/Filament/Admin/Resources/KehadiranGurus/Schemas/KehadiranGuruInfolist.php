<?php

namespace App\Filament\Admin\Resources\KehadiranGurus\Schemas;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class KehadiranGuruInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Jadwal')
                    ->schema([
                        TextEntry::make('tanggal')
                            ->label('Tanggal')
                            ->date('d M Y'),
                        TextEntry::make('jadwal.hari')
                            ->label('Hari')
                            ->badge(),
                        TextEntry::make('jadwal.jam_ke')
                            ->label('Jam Ke'),
                        TextEntry::make('jadwal.jam_mulai')
                            ->label('Jam Mulai'),
                        TextEntry::make('jadwal.jam_selesai')
                            ->label('Jam Selesai'),
                        TextEntry::make('jadwal.kelas.nama')
                            ->label('Kelas'),
                        TextEntry::make('jadwal.mataPelajaran.nama')
                            ->label('Mata Pelajaran'),
                    ])
                    ->columns(2),

                Section::make('Informasi Guru')
                    ->schema([
                        TextEntry::make('guru.nama')
                            ->label('Nama Guru'),
                        TextEntry::make('guru.nip')
                            ->label('NIP'),
                        TextEntry::make('guru.email')
                            ->label('Email'),
                        TextEntry::make('guru.no_telp')
                            ->label('No. Telepon'),
                    ])
                    ->columns(2),

                Section::make('Status Kehadiran')
                    ->schema([
                        TextEntry::make('status_kehadiran')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'hadir' => 'success',
                                'telat' => 'warning',
                                'tidak_hadir' => 'danger',
                                'izin' => 'info',
                                'sakit' => 'gray',
                                default => 'gray',
                            }),
                        TextEntry::make('waktu_datang')
                            ->label('Waktu Datang'),
                        TextEntry::make('durasi_keterlambatan')
                            ->label('Durasi Keterlambatan')
                            ->suffix(' menit')
                            ->color('warning'),
                        TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Informasi Tambahan')
                    ->schema([
                        TextEntry::make('diinputOleh.name')
                            ->label('Diinput Oleh'),
                        TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime('d M Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Diupdate Pada')
                            ->dateTime('d M Y H:i'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
