<?php

namespace App\Filament\Admin\Resources\MataPelajaranResource\Pages;

use App\Filament\Admin\Resources\MataPelajaranResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMataPelajaran extends CreateRecord
{
    protected static string $resource = MataPelajaranResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Mata pelajaran berhasil ditambahkan';
    }
}