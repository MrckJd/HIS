<?php

namespace App\Filament\AvatarProvider;

use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Color\Rgb;

class UiAvatarsProvider extends \Filament\AvatarProviders\UiAvatarsProvider
{
    public function get(Model|Authenticatable $record): string
    {
        $firstName = substr($record->first_name,0 , 1);
        $lastName = substr($record->surname, 0, 1);

        $backgroundColor = Rgb::fromString('rgb('.FilamentColor::getColors()['primary'][500].')')->toHex();

        return 'https://ui-avatars.com/api/?name='.urlencode($firstName.$lastName).'&color=FFFFFF&background='.str($backgroundColor)->after('#');
    }
}
