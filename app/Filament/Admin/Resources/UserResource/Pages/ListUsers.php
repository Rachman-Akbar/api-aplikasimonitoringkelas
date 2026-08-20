<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Filament\Imports\UserImporter;
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
            Actions\ImportAction::make()
                ->importer(UserImporter::class)
                ->modalHeading('Impor User')
                ->modalDescription('Unggah file CSV atau XLSX dengan format yang sesuai. Pastikan Anda menggunakan template yang telah disediakan. Template bisa diunduh menggunakan tombol "Download Template".')
                ->maxRows(10000),
        ];
    }

    public function getMaxWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }
}
