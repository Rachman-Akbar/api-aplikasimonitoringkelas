<?php

namespace App\Filament\Admin\Resources\MataPelajaranResource\Pages;

use App\Filament\Admin\Resources\MataPelajaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMataPelajaran extends EditRecord
{
    protected static string $resource = MataPelajaranResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Mata pelajaran berhasil diperbarui';
    }
}