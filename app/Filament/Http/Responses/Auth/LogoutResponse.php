<?php

namespace App\Filament\Http\Responses\Auth;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LogoutResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        // Always redirect to the login page after logout for the admin panel
        $loginUrl = Filament::hasLogin() ? Filament::getLoginUrl() : Filament::getUrl();
        
        return redirect()->to($loginUrl);
    }
}