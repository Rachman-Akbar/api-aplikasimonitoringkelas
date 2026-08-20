<?php

namespace App\Filament\Admin\Resources\SiswaResource\Pages;

use App\Filament\Admin\Resources\SiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSiswa extends EditRecord
{
    protected static string $resource = SiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Sinkronisasi kelas_id ke user jika siswa punya user account
        if ($this->record->user_id) {
            \App\Models\User::where('id', $this->record->user_id)->update([
                'kelas_id' => $this->record->kelas_id,
            ]);
        }
    }
}
