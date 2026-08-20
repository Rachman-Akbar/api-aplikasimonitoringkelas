<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        // Call the parent authenticate method to validate credentials
        $result = parent::authenticate();
        
        if ($result && auth()->check()) {
            $user = auth()->user();
            
            // Check if user has admin or kepsek role
            if ($user->role !== 'admin' && $user->role !== 'kepsek') {
                // If not an admin, logout and show error
                auth()->logout();
                
                throw ValidationException::withMessages([
                    'data' => ['Access denied. Only admin users can access this panel.'],
                ]);
            }
        }
        
        return $result;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->autocomplete(false)
                    ->placeholder('email@example.com'),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->required()
                    ->autocomplete(false),
            ]);
    }
}