<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    protected string $view = 'filament.pages.auth.login';

    public function mount(): void
    {
        $this->form->fill([
            'email' => 'admin@carlmabugay.com',
            'password' => 'password',
        ]);
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'Welcome to Arian';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Enter your credentials to access your account.';
    }

    public function hasLogo(): bool
    {
        return false;
    }
}
