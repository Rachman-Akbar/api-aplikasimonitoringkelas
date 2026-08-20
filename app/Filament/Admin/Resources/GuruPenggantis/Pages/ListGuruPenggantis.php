<?php

namespace App\Filament\Admin\Resources\GuruPenggantis\Pages;

use App\Filament\Admin\Resources\GuruPenggantis\GuruPenggantiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListGuruPenggantis extends ListRecords
{
    protected static string $resource = GuruPenggantiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportBulkAction::make()
                ->exports([
                    ExcelExport::make('export')
                        ->fromTable()
                        ->withFilename('guru-pengganti-rekapan-' . date('Y-m-d'))
                        ->withWriterType(\Maatwebsite\Excel\Excel::XLSX),
                ])
                ->label('Export Rekapan')
                ->color('primary')
                ->icon('heroicon-o-arrow-down-tray'),
            // Admin hanya monitoring, guru yang approve
        ];
    }
}
