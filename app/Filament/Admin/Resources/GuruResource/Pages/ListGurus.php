<?php

namespace App\Filament\Admin\Resources\GuruResource\Pages;

use App\Filament\Admin\Resources\GuruResource;
use App\Filament\Imports\GuruImporter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;

class ListGurus extends ListRecords
{
    protected static string $resource = GuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('downloadTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('secondary')
                ->url(route('download.template.guru'))
                ->openUrlInNewTab(false),
            Actions\ImportAction::make()
                ->importer(GuruImporter::class)
                ->modalHeading('Impor Guru')
                ->modalDescription('Unggah file CSV atau XLSX dengan format yang sesuai. Pastikan Anda menggunakan template yang telah disediakan. Template bisa diunduh menggunakan tombol "Download Template".')
                ->maxRows(10000),
        ];
    }

    public function getMaxWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }
}
