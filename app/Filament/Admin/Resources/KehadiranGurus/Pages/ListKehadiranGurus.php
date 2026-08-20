<?php

namespace App\Filament\Admin\Resources\KehadiranGurus\Pages;

use App\Filament\Admin\Resources\KehadiranGurus\KehadiranGuruResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListKehadiranGurus extends ListRecords
{
    protected static string $resource = KehadiranGuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportBulkAction::make()
                ->exports([
                    ExcelExport::make('export')
                        ->fromTable()
                        ->withFilename('kehadiran-guru-rekapan-' . date('Y-m-d'))
                        ->withWriterType(\Maatwebsite\Excel\Excel::XLSX),
                ])
                ->label('Export Rekapan')
                ->color('primary')
                ->icon('heroicon-o-arrow-down-tray'),
            // Admin hanya monitoring
        ];
    }
}
