<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    protected static ?string $title = 'Dashboard';

    public function mount(): void
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            // Redirect admin to main dashboard
            return;
        } elseif ($user->isKurikulum()) {
            // Redirect kurikulum to relevant page
            return;
        } elseif ($user->isGuru()) {
            // Redirect guru to relevant page
            return;
        } elseif ($user->isSiswa()) {
            // Redirect siswa to relevant page
            return;
        }
    }
}