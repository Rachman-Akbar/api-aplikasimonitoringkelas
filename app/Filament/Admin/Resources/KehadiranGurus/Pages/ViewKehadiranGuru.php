<?php

namespace App\Filament\Admin\Resources\KehadiranGurus\Pages;

use App\Filament\Admin\Resources\KehadiranGurus\KehadiranGuruResource;
use Filament\Resources\Pages\ViewRecord;

class ViewKehadiranGuru extends ViewRecord
{
    protected static string $resource = KehadiranGuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Admin hanya monitoring, tidak edit/delete
        ];
    }
}
