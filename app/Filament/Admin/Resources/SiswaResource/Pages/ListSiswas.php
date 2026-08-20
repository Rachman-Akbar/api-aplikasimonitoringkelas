<?php

namespace App\Filament\Admin\Resources\SiswaResource\Pages;

use App\Filament\Admin\Resources\SiswaResource;
use App\Filament\Imports\SiswaImporter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;

class ListSiswas extends ListRecords
{
    protected static string $resource = SiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('downloadTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('secondary')
                ->url(route('download.template.siswa'))
                ->openUrlInNewTab(false),
            Actions\ImportAction::make()
                ->importer(SiswaImporter::class)
                ->modalHeading('Impor Siswa')
                ->modalDescription('Unggah file CSV atau XLSX dengan format yang sesuai. Pastikan Anda menggunakan template yang telah disediakan. Template bisa diunduh menggunakan tombol "Download Template".')
                ->maxRows(10000),
        ];
    }

    public function getMaxWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }
}
