<?php

namespace App\Filament\Admin\Resources\IzinGurus\Pages;

use App\Filament\Admin\Resources\IzinGurus\IzinGuruResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewIzinGuru extends ViewRecord
{
    protected static string $resource = IzinGuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
