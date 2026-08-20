<?php

namespace App\Filament\Admin\Resources\KehadiranGurus\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Schemas\Schema;

class KehadiranGuruForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Attendance Information')
                    ->description('Fill in the teacher attendance details')
                    ->schema([
                        Select::make('guru_id')
                            ->label('Teacher')
                            ->relationship('guru', 'nama')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('jadwal_id')
                            ->label('Schedule')
                            ->relationship('jadwal', 'id')
                            ->getOptionLabelFromRecordUsing(
                                fn($record) =>
                                "{$record->kelas->nama} - {$record->mataPelajaran->nama} - {$record->hari} Jam {$record->jam_ke}"
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        DatePicker::make('tanggal')
                            ->label('Date')
                            ->required()
                            ->default(now()),

                        Select::make('status_kehadiran')
                            ->label('Status')
                            ->options([
                                'hadir' => 'Hadir',
                                'telat' => 'Telat',
                                'tidak_hadir' => 'Tidak Hadir',
                                'izin' => 'Izin',
                                'sakit' => 'Sakit',
                            ])
                            ->default('hadir')
                            ->required()
                            ->reactive(),

                        TextInput::make('waktu_datang')
                            ->label('Arrival Time')
                            ->placeholder('07:00:00 AM')
                            ->helperText('Format: HH:MM:SS AM/PM'),

                        TextInput::make('durasi_keterlambatan')
                            ->label('Late Duration (minutes)')
                            ->numeric()
                            ->suffix('min'),

                        Textarea::make('keterangan')
                            ->label('Notes')
                            ->rows(3)
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
