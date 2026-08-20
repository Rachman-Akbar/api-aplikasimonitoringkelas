<?php

namespace App\Filament\Actions;

use App\Imports\FirstSheetImport;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;

class ImportAction
{
    public static function make(string $importClass, ?string $modelClass = null): Action
    {
        return Action::make('import')
            ->label('Import')
            ->icon('heroicon-o-arrow-up-tray')
            ->schema([
                FileUpload::make('file')
                    ->label('File Import')
                    ->required()
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                        'text/csv',
                        'text/plain',
                        'application/csv',
                        '.xlsx',
                        '.xls',
                        '.csv',
                    ])
                    ->maxSize(10240)
                    ->storeFiles(false),
            ])
            ->action(function (array $data) use ($importClass): void {
                try {
                    $filePath = self::resolveFilePath($data['file'] ?? null);
                    Excel::import(new FirstSheetImport(new $importClass()), $filePath);

                    Notification::make()
                        ->title('Import Berhasil')
                        ->body('Data pada Sheet Data berhasil diproses.')
                        ->success()
                        ->send();
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title('Import Gagal')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    private static function resolveFilePath(mixed $file): string
    {
        if (is_string($file) && is_file($file)) {
            return $file;
        }

        if (is_array($file) && $file !== []) {
            return self::resolveFilePath(reset($file));
        }

        if ($file instanceof UploadedFile || (is_object($file) && method_exists($file, 'getRealPath'))) {
            $path = $file->getRealPath();

            if (is_string($path) && is_file($path)) {
                return $path;
            }
        }

        throw new \RuntimeException('File import tidak ditemukan atau format upload tidak dikenali.');
    }
}
