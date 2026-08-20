<?php

namespace App\Filament\Admin\Resources\GuruPenggantis\Pages;

use App\Filament\Admin\Resources\GuruPenggantis\GuruPenggantiResource;
use Filament\Resources\Pages\ViewRecord;

class ViewGuruPengganti extends ViewRecord
{
    protected static string $resource = GuruPenggantiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Admin hanya monitoring, tidak edit/delete
        ];
    }
}
