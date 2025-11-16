<?php

namespace App\Http\Response;

use App\Enum\UserRole;
use App\Models\User;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LogInResponse implements Responsable
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        /** @var User $user */
        $user = $request->user();

        $route = match ($user->role) {
            UserRole::ROOT->value=> 'filament.root.pages.dashboard',
            UserRole::ADMIN->value=> 'filament.admin.pages.dashboard',
            UserRole::PROVIDER->value=> 'filament.serviceProvider.pages.dashboard',
            UserRole::ENCODER->value=> 'filament.encoder.home',
            UserROle::SUPERVISOR->value=> 'filament.supervisor.pages.dashboard',
            default => 'filament.admin.auth.login',

        };

        return redirect()->route($route);
    }
}
