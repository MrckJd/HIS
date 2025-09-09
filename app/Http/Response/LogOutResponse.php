<?php

namespace App\Http\Response;

use Filament\Http\Responses\Auth\Contracts\LogoutResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

class LogOutResponse implements Responsable
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        return redirect()->route('filament.admin.auth.login');
    }
}
