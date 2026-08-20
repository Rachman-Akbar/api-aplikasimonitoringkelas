<?php

namespace App\Filament\Admin\Resources\Kehadirans\Pages;

use App\Filament\Admin\Resources\Kehadirans\KehadiranResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewKehadiran extends ViewRecord
{
    protected static string $resource = KehadiranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
