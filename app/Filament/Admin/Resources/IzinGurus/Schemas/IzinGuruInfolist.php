<?php

namespace App\Filament\Admin\Resources\IzinGurus\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class IzinGuruInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('guru_id')
                    ->numeric(),
                TextEntry::make('tanggal_mulai')
                    ->date(),
                TextEntry::make('tanggal_selesai')
                    ->date(),
                TextEntry::make('durasi_hari')
                    ->numeric(),
                TextEntry::make('jenis_izin'),
                TextEntry::make('file_surat'),
                TextEntry::make('status_approval'),
                TextEntry::make('disetujui_oleh')
                    ->numeric(),
                TextEntry::make('tanggal_approval')
                    ->dateTime(),
                TextEntry::make('catatan_approval')
                    ->label('Catatan Approval'),
                TextEntry::make('deleted_at')
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
