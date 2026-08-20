<?php

namespace App\Filament\Admin\Resources\Kehadirans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class KehadiranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('siswa_id')
                    ->label('Siswa')
                    ->relationship('siswa', 'nama')
                    ->searchable(['nama', 'nis'])
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->nis} - {$record->nama} ({$record->kelas->nama})")
                    ->preload()
                    ->required(),
                Select::make('jadwal_id')
                    ->label('Jadwal Pelajaran')
                    ->relationship('jadwal', 'id')
                    ->getOptionLabelFromRecordUsing(
                        fn($record) =>
                        "{$record->kelas->nama} - {$record->mataPelajaran->nama} - {$record->hari} (Jam {$record->jam_ke})"
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->default(now()),
                Select::make('status')
                    ->label('Status Kehadiran')
                    ->options([
                        'hadir' => 'Hadir',
                        'sakit' => 'Sakit',
                        'izin' => 'Izin',
                        'tidak_hadir' => 'Tidak Hadir'
                    ])
                    ->default('hadir')
                    ->required(),
                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(3)
                    ->columnSpanFull()
                    ->placeholder('Contoh: Sakit demam, Izin urusan keluarga, dll'),
            ])
            ->columns(2);
    }
}
