<?php

namespace App\Filament\Admin\Resources\MataPelajaranResource\Pages;

use App\Filament\Admin\Resources\MataPelajaranResource;
use App\Filament\Actions\ImportAction;
use App\Imports\MataPelajaranImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;

class ListMataPelajarans extends ListRecords
{
    protected static string $resource = MataPelajaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('downloadTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('secondary')
                ->url(route('download.template.mata-pelajaran'))
                ->openUrlInNewTab(false),
            ImportAction::make(MataPelajaranImport::class)
                ->modalHeading('Impor Mata Pelajaran')
                ->modalDescription('Unggah file XLSX, XLS, atau CSV. Untuk template XLSX dua sheet, hanya Sheet Data yang akan diimport.'),
        ];
    }

    public function getMaxWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }
}
