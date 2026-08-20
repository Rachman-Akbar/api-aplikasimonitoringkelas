<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;

class ImportAction
{
    public static function make(string $importClass, string $modelClass)
    {
        return Action::make('import')
            ->label('Import Excel')
            ->icon('heroicon-o-arrow-down-tray')
            ->form([
                \Filament\Forms\Components\FileUpload::make('file')
                    ->label('File Excel')
                    ->required()
                    // Accept Excel files and CSV files. Some browsers/OS report CSV as text/csv or text/plain,
                    // so include common CSV mime types and the .csv extension to be permissive.
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                        'text/csv',
                        'text/plain',
                        'application/csv',
                        '.csv',
                    ])
                    ->maxSize(10240) // 10MB
                    ->storeFiles(false),
            ])
            ->action(function (array $data) use ($importClass, $modelClass) {
                try {
                    $file = $data['file'];

                    $filePath = null;

                    // Handle different file types that Filament might return
                    if (is_string($file)) {
                        // If it's already a path string
                        $filePath = $file;
                    } elseif (is_array($file) && count($file) > 0) {
                        // If it's an array of file paths (common with multiple files)
                        $filePath = $file[0];
                    } elseif ($file instanceof UploadedFile) {
                        // Handle regular uploaded files
                        $filePath = $file->getRealPath();
                    } elseif (is_object($file) && method_exists($file, 'getRealPath')) {
                        // Handle other file-like objects
                        $filePath = $file->getRealPath();
                    } else {
                        throw new \Exception("Format file tidak dikenali: " . gettype($file));
                    }

                    // Ensure we have a valid file path
                    if (!$filePath || !file_exists($filePath)) {
                        throw new \Exception("File tidak ditemukan atau tidak valid: " . ($filePath ?? 'unknown'));
                    }

                    // Perform the import
                    Excel::import(new $importClass, $filePath);

                    // Show success notification
                    Notification::make()
                        ->title('Import Berhasil')
                        ->body('Data berhasil diimport dari file Excel.')
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    // Show error notification
                    Notification::make()
                        ->title('Import Gagal')
                        ->body('Error: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}
