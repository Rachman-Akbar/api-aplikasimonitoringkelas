<?php

namespace App\Filament\Admin\Resources\GuruPenggantis\Pages;

use App\Filament\Admin\Resources\GuruPenggantis\GuruPenggantiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGuruPengganti extends EditRecord
{
    protected static string $resource = GuruPenggantiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
