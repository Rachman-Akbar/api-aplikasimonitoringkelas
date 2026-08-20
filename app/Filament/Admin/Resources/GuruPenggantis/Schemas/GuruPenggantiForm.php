<?php

namespace App\Filament\Admin\Resources\GuruPenggantis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class GuruPenggantiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('jadwal_id')
                    ->label('Jadwal')
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
                    ->label('Tanggal')
                    ->required()
                    ->native(false),

                Select::make('guru_asli_id')
                    ->label('Guru Asli')
                    ->relationship('guruAsli', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('guru_pengganti_id')
                    ->label('Guru Pengganti')
                    ->relationship('guruPengganti', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),

                Textarea::make('alasan_penggantian')
                    ->label('Alasan Penggantian')
                    ->rows(3)
                    ->columnSpanFull(),

                Select::make('status_penggantian')
                    ->label('Status')
                    ->options([
                        'dijadwalkan' => 'Dijadwalkan',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ])
                    ->default('dijadwalkan')
                    ->required(),

                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
