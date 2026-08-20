<?php

namespace App\Http\Controllers;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\LogoutResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FilamentLogoutController extends Controller
{
    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): LogoutResponse
    {
        // Logout from the current guard
        $guard = Filament::auth();
        $guard->logout();
        
        // Invalidate the session
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Return the logout response
        return app(LogoutResponse::class);
    }
}
