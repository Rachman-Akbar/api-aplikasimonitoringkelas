<?php

namespace App\Filament\Admin\Resources\JadwalResource\Pages;

use App\Filament\Admin\Resources\JadwalResource;
use App\Filament\Actions\ImportAction;
use App\Imports\JadwalImport;
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
            ImportAction::make(JadwalImport::class)
                ->modalHeading('Impor Jadwal')
                ->modalDescription('Unggah file XLSX, XLS, atau CSV. Untuk template XLSX dua sheet, hanya Sheet Data yang akan diimport.'),
        ];
    }

    public function getMaxWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }
}
