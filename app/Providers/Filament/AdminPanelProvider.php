<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Login\Login;
use App\Filament\Panel\Page\Auth\Profile;
use App\Filament\Resources\DashBoardWidgetResource\Widgets\MunicipalityChart;
use App\Filament\Resources\DashBoardWidgetResource\Widgets\ServicesPieChart;
use App\Http\Middleware\CheckUserPanel;
use Croustibat\FilamentJobsMonitor\FilamentJobsMonitorPlugin;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
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
            ->path('')
            ->login(Login::class)
            ->profile(Profile::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->plugins([
                FilamentJobsMonitorPlugin::make()
                    ->enableNavigation(fn()=>in_array(Filament::getCurrentPanel()->getId(), ['root', 'admin']))
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                MunicipalityChart::class,
                ServicesPieChart::class,
            ])
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
                CheckUserPanel::class,
                Authenticate::class,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('60s');

    }
}
