<?php

namespace App\Filament\Panel\Page\Auth;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Pages\Auth\EditProfile;
use SensitiveParameter;

class Profile extends EditProfile
{
    protected $listeners = [
        'refresh' => '$refresh',
    ];

    public static function isSimple(): bool
    {
        return false;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changePassword')
                ->label('Change Password')
                ->icon('heroicon-o-lock-closed')
                ->color('secondary')
                ->button()
        ];
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Section::make('Information')
                            ->description('Update your profile information.')
                            ->aside()
                            ->schema([
                                $this->getEmailFormComponent()
                                    ->prefixIcon('heroicon-o-at-symbol')
                                    ->readOnly()
                                    ->dehydrated(false)
                                    ->helperText('You cannot change your email address.'),
                                $this->getNameFormComponent()
                                    ->prefixIcon('heroicon-o-tag')
                                    ->helperText('Enter your full legal name with proper capitalization.')
                                    ->dehydrateStateUsing(fn ($state) => $this->formatName($state)),
                            ]),
                    ])
                    ->operation('edit')
                    ->model($this->getUser())
                    ->statePath('data')
                    ->inlineLabel(),
            ),
        ];
    }
}
