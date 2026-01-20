<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Livewire\Topbar;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors([
                'primary' => [
                    50 => 'oklch(0.97 0.03 160)',
                    100 => 'oklch(0.94 0.06 160)',
                    200 => 'oklch(0.88 0.11 160)',
                    300 => 'oklch(0.82 0.17 160)',
                    400 => 'oklch(0.78 0.21 160)',
                    500 => 'oklch(0.83 0.24 160)', // ≈ #00EB97
                    600 => 'oklch(0.72 0.22 160)',
                    700 => 'oklch(0.61 0.19 160)',
                    800 => 'oklch(0.50 0.15 160)',
                    900 => 'oklch(0.42 0.12 160)',
                    950 => 'oklch(0.30 0.08 160)',
                ],
                'gray' => Color::Slate,
            ])
            ->login(Login::class)
            ->font('Plus Jakarta Sans')
            ->brandName('Arian')
            ->topNavigation()
            ->topbarLivewireComponent(Topbar::class)
            ->maxContentWidth(Width::SevenExtraLarge)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->databaseNotifications();
    }
}
