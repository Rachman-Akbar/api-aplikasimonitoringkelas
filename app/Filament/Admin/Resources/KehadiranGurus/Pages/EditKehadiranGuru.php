<?php

namespace App\Filament\Admin\Resources\KehadiranGurus\Pages;

use App\Filament\Admin\Resources\KehadiranGurus\KehadiranGuruResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKehadiranGuru extends EditRecord
{
    protected static string $resource = KehadiranGuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
