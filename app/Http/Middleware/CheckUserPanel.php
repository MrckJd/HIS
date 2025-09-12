<?php

namespace App\Http\Middleware;

use App\Enum\UserRole;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserPanel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $panelId = Filament::getCurrentPanel()?->getId();

        if($user->role !== $panelId) {

                $route = match ($user->role) {
                UserRole::ROOT->value=> 'filament.root.pages.dashboard',
                UserRole::ADMIN->value=> 'filament.admin.pages.dashboard',
                UserRole::PROVIDER->value=> 'filament.serviceProvider.pages.dashboard',
                UserRole::ENCODER->value=> 'filament.encoder.home',
                default => 'filament.admin.auth.login',

            };

            return redirect()->route($route);
        }

        return $next($request);
    }
}
