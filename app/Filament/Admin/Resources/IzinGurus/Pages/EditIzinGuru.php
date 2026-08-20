<?php

namespace App\Filament\Admin\Resources\IzinGurus\Pages;

use App\Filament\Admin\Resources\IzinGurus\IzinGuruResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditIzinGuru extends EditRecord
{
    protected static string $resource = IzinGuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
