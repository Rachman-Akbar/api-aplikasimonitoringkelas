<?php

namespace App\Filament\Admin\Resources\Kehadirans\Pages;

use App\Filament\Admin\Resources\Kehadirans\KehadiranResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListKehadirans extends ListRecords
{
    protected static string $resource = KehadiranResource::class;

    protected function getHeaderActions(): array
    {
        // Keep header minimal for admin (export is provided as a table bulk action)
        return [
            // no header export action to avoid Alpine scope errors
        ];
    }
}
