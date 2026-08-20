<?php

namespace App\Filament\Admin\Resources\SiswaResource\Pages;

use App\Filament\Admin\Resources\SiswaResource;
use App\Filament\Actions\ImportAction;
use App\Imports\SiswaImport;
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
            ImportAction::make(SiswaImport::class)
                ->modalHeading('Impor Siswa')
                ->modalDescription('Unggah file XLSX, XLS, atau CSV. Untuk template XLSX dua sheet, hanya Sheet Data yang akan diimport.'),
        ];
    }

    public function getMaxWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }
}
