<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Illuminate\Support\Facades\URL;

class BackAction extends Action{

    protected function setUp(): void
    {
        parent::setUp();

        $this->name('back');

        $this->label('Back');

        $this->icon('heroicon-o-arrow-left');
    }


}
