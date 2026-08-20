<?php

namespace App\Filament\Admin\Resources\KelasResource\Pages;

use App\Filament\Admin\Resources\KelasResource;
use App\Filament\Actions\ImportAction;
use App\Imports\KelasImport;
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
            ImportAction::make(KelasImport::class)
                ->modalHeading('Impor Kelas')
                ->modalDescription('Unggah file XLSX, XLS, atau CSV. Untuk template XLSX dua sheet, hanya Sheet Data yang akan diimport.'),
        ];
    }

    public function getMaxWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }
}
