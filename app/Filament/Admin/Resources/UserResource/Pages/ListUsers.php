<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Filament\Actions\ImportAction;
use App\Imports\UserImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('downloadTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('secondary')
                ->url(route('download.template.user'))
                ->openUrlInNewTab(false),
            ImportAction::make(UserImport::class)
                ->modalHeading('Impor User')
                ->modalDescription('Unggah file XLSX, XLS, atau CSV. Untuk template XLSX dua sheet, hanya Sheet Data yang akan diimport.'),
        ];
    }

    public function getMaxWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }
}
