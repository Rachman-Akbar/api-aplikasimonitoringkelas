<?php

namespace App\Filament\Admin\Resources\GuruResource\Pages;

use App\Filament\Admin\Resources\GuruResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGuru extends EditRecord
{
    protected static string $resource = GuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Sinkronisasi guru_id ke user jika guru punya user account
        if ($this->record->user_id) {
            \App\Models\User::where('id', $this->record->user_id)->update([
                'guru_id' => $this->record->id,
            ]);
        }
    }
}
