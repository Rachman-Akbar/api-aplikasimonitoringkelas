<?php

namespace App\Filament\Admin\Resources\Kehadirans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class KehadiranInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('siswa_id')
                    ->numeric(),
                TextEntry::make('jadwal_id')
                    ->numeric(),
                TextEntry::make('tanggal')
                    ->date(),
                TextEntry::make('status'),
                TextEntry::make('deleted_at')
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
