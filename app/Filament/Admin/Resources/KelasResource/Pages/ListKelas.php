<?php

namespace App\Filament\Admin\Resources\KelasResource\Pages;

use App\Filament\Admin\Resources\KelasResource;
use App\Filament\Imports\KelasImporter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;

class ListKelas extends ListRecords
{
    protected static string $resource = KelasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('downloadTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('secondary')
                ->url(route('download.template.kelas'))
                ->openUrlInNewTab(false),
            Actions\ImportAction::make()
                ->importer(KelasImporter::class)
                ->modalHeading('Impor Kelas')
                ->modalDescription('Unggah file CSV atau XLSX dengan format yang sesuai. Pastikan Anda menggunakan template yang telah disediakan. Template bisa diunduh menggunakan tombol "Download Template".')
                ->maxRows(10000),
        ];
    }

    public function getMaxWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }
}
