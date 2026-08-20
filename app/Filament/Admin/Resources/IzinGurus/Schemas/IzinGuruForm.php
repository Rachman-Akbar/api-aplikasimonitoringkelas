<?php

namespace App\Filament\Admin\Resources\IzinGurus\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class IzinGuruForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('guru_id')
                    ->label('Guru')
                    ->relationship('guru', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $tanggal_selesai = $get('tanggal_selesai');
                        if ($state && $tanggal_selesai) {
                            $durasi = \Carbon\Carbon::parse($state)->diffInDays(\Carbon\Carbon::parse($tanggal_selesai)) + 1;
                            $set('durasi_hari', $durasi);
                        }
                    }),
                DatePicker::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $tanggal_mulai = $get('tanggal_mulai');
                        if ($state && $tanggal_mulai) {
                            $durasi = \Carbon\Carbon::parse($tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($state)) + 1;
                            $set('durasi_hari', $durasi);
                        }
                    }),
                TextInput::make('durasi_hari')
                    ->label('Durasi (Hari)')
                    ->required()
                    ->numeric()
                    ->readOnly()
                    ->suffix(' hari'),
                Select::make('jenis_izin')
                    ->label('Jenis Izin')
                    ->options([
                        'sakit' => 'Sakit',
                        'izin' => 'Izin',
                        'cuti' => 'Cuti',
                        'dinas_luar' => 'Dinas Luar',
                        'lainnya' => 'Lainnya',
                    ])
                    ->required(),
                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('file_surat')
                    ->label('File Surat')
                    ->helperText('Path file surat izin'),
                Select::make('status_approval')
                    ->label('Status Approval')
                    ->options([
                        'pending' => 'Pending',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak'
                    ])
                    ->default('pending')
                    ->required(),
                Select::make('disetujui_oleh')
                    ->label('Disetujui Oleh')
                    ->relationship('disetujuiOleh', 'name')
                    ->searchable()
                    ->preload(),
                DateTimePicker::make('tanggal_approval')
                    ->label('Tanggal Approval')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i'),
            ])
            ->columns(2);
    }
}
