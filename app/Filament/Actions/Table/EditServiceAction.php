<?php

namespace App\Filament\Actions\Table;

use App\Models\Service;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;

class EditServiceAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->modalAlignment(Alignment::Center);

        $this->modalWidth(MaxWidth::Large);

        $this->modalSubmitActionLabel('Add');

        $this->closeModalByClickingAway(false);

        $this->modalDescription('Fill in the details of the new service.');

        $this->fillForm(function (Service $service) {
            return [
                'name' => $service->name,
            ];
        });

        $this->form([
                    TextInput::make('name')
                            ->required()
                            ->maxLength(255),
            ]);

        $this->action(fn($data, Service $service) => $service->update($data));

        $this->sendSuccessNotification();

        $this->successNotificationTitle('Service added successfully');
    }

}
