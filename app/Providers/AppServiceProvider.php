<?php

namespace App\Providers;

use Filament\Actions\CreateAction;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
    */
    public function boot(): void
    {
        FilamentView::registerRenderHook(PanelsRenderHook::HEAD_START, fn() => Blade::render ('@vite(\'resources/css/app.css\')'));

        Table::configureUsing(function ($table){
            $table
                ->striped();
        });

        CreateAction::configureUsing(function ($action){
            $action
                ->icon('heroicon-o-plus');
        });

        $this->app->bind(\Filament\Http\Responses\Auth\Contracts\LogoutResponse::class, \App\Http\Response\LogOutResponse::class);
    }
}
