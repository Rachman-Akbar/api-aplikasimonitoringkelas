<?php

namespace App\Filament\Admin\Resources\GuruPenggantis\Schemas;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class GuruPenggantiInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Guru Pengganti')
                    ->schema([
                        TextEntry::make('jadwal.kelas.nama')
                            ->label('Kelas'),
                        TextEntry::make('jadwal.mataPelajaran.nama')
                            ->label('Mata Pelajaran'),
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
                    ])
                    ->columns(2),

                Section::make('Informasi Guru')
                    ->schema([
                        TextEntry::make('guruAsli.nama')
                            ->label('Guru Asli'),
                        TextEntry::make('guruAsli.nip')
                            ->label('NIP Guru Asli'),
                        TextEntry::make('guruPengganti.nama')
                            ->label('Guru Pengganti')
                            ->color('success')
                            ->weight('bold'),
                        TextEntry::make('guruPengganti.nip')
                            ->label('NIP Guru Pengganti'),
                    ])
                    ->columns(2),

                Section::make('Detail Penggantian')
                    ->schema([
                        TextEntry::make('status_penggantian')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'dijadwalkan' => 'warning',
                                'selesai' => 'success',
                                'dibatalkan' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                        TextEntry::make('catatan_approval')
                            ->label('Catatan Approval')
                            ->columnSpanFull(),
                        TextEntry::make('disetujuiOleh.name')
                            ->label('Disetujui Oleh'),
                    ])
                    ->columns(2),

                Section::make('Informasi Tambahan')
                    ->schema([
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
