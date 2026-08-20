<?php

namespace App\Filament\Admin\Resources\IzinGurus\Pages;

use App\Filament\Admin\Resources\IzinGurus\IzinGuruResource;
use App\Models\IzinGuru;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListIzinGurus extends ListRecords
{
    protected static string $resource = IzinGuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportBulkAction::make()
                ->exports([
                    ExcelExport::make('export')
                        ->fromTable()
                        ->withFilename('izin-guru-rekapan-' . date('Y-m-d'))
                        ->withWriterType(\Maatwebsite\Excel\Excel::XLSX),
                ])
                ->label('Export Rekapan')
                ->color('primary')
                ->icon('heroicon-o-arrow-down-tray'),
            // Admin hanya approve/reject izin guru, tidak perlu import
        ];
    }
}
