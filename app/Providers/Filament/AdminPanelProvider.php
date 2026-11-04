<?php

namespace App\Providers\Filament;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\View\PanelsRenderHook; // <--- Import kelas ini di bagian atas
use Illuminate\Support\Facades\Blade;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->plugins([
                // DAFTARKAN SHIELD DI SINI (Navigasi peran disembunyikan dari sidebar sesuai instruksi)
                FilamentShieldPlugin::make()
                    ->registerNavigation(false),
            ])
            ->globalSearch(false)
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->favicon(asset('icons/icon-192x192.png'))
            ->brandLogo(fn () => (! filament()->auth()->check() || request()->is('*/login*') || request()->routeIs('filament.admin.auth.*'))
                ? asset('logo-login.png')
                : asset('logo-navbar.png')
            )
            ->brandLogoHeight(fn () => (! filament()->auth()->check() || request()->is('*/login*') || request()->routeIs('filament.admin.auth.*'))
                ? '5.5rem'
                : '2.5rem'
            )
            ->colors([
                'primary' => Color::Emerald,
                'gray' => Color::Slate,
            ])
            ->navigationGroups([
                'Presensi & Kehadiran',
                'Data Akademik',
                'Perpustakaan',
                'Pengaturan & Sistem',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                // AccountWidget::class,
                // FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn (): string => Blade::render('@include("filament.theme.ambient-glow")'),
            )
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): string => Blade::render('@include("filament.theme.styles")'),
            )
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn (): string => Blade::render('@include("filament.components.google-sign-in-button")'),
            )
            ->renderHook(
                PanelsRenderHook::SIDEBAR_FOOTER,
                fn (): string => Blade::render('@include("filament.components.pwa-install-button")'),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END, // Menempatkan tag di akhir bagian <head>
                fn (): string => Blade::render('
                    <!-- PWA Meta & Manifest -->
                    <link rel="manifest" href="' . asset('manifest.json') . '" />
                    <meta name="theme-color" content="#10b981" />
                    <meta name="mobile-web-app-capable" content="yes" />
                    <meta name="apple-mobile-web-app-capable" content="yes" />
                    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
                    <meta name="apple-mobile-web-app-title" content="maisba" />
                    <link rel="apple-touch-icon" href="' . asset('icons/apple-touch-icon.png') . '" />

                    <!-- Open Graph / Meta Sosial Media -->
                    <meta property="og:title" content="Absaka - MA Islamiyah Balen" />
                    <meta property="og:description" content="Sistem Presensi & Perpustakaan Digital MA Islamiyah Balen (maisba) berbasis QR Code & RFID terintegrasi secara real-time." />
                    <meta property="og:image" content="' . asset('images/og-preview.jpg') . '" />
                    <meta property="og:url" content="' . url('/') . '" />
                    <meta property="og:type" content="website" />

                    <!-- Twitter/X Card -->
                    <meta name="twitter:card" content="summary_large_image" />
                    <meta name="twitter:title" content="Absaka - MA Islamiyah Balen" />
                    <meta name="twitter:description" content="Sistem Presensi & Perpustakaan Digital MA Islamiyah Balen (maisba) berbasis QR Code & RFID terintegrasi secara real-time." />
                    <meta name="twitter:image" content="' . asset('images/og-preview.png') . '" />
                '),
            )
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
