<?php

namespace App\Filament\Admin\Resources\JadwalResource\Pages;

use App\Filament\Admin\Resources\JadwalResource;
use App\Filament\Imports\JadwalImporter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;

class ListJadwals extends ListRecords
{
    protected static string $resource = JadwalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('downloadTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('secondary')
                ->url(route('download.template.jadwal'))
                ->openUrlInNewTab(false),
            Actions\ImportAction::make()
                ->importer(JadwalImporter::class)
                ->modalHeading('Impor Jadwal')
                ->modalDescription('Unggah file CSV atau XLSX dengan format yang sesuai. Pastikan Anda menggunakan template yang telah disediakan. Template bisa diunduh menggunakan tombol "Download Template".')
                ->maxRows(10000),
        ];
    }

    public function getMaxWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }
}
