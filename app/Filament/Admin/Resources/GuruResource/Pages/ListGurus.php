<?php

namespace App\Filament\Admin\Resources\GuruResource\Pages;

use App\Filament\Admin\Resources\GuruResource;
use App\Filament\Actions\ImportAction;
use App\Imports\GuruImport;
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
            ImportAction::make(GuruImport::class)
                ->modalHeading('Impor Guru')
                ->modalDescription('Unggah file XLSX, XLS, atau CSV. Untuk template XLSX dua sheet, hanya Sheet Data yang akan diimport.'),
        ];
    }

    public function getMaxWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }
}
