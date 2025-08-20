<?php

namespace App\Filament\Actions;

use App\Models\Service;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;

class CreateServiceAction extends Action {

    protected function setUp(): void
    {
        parent::setUp();

        $this->modalAlignment(Alignment::Center);

        $this->modalWidth(MaxWidth::Large);

        $this->modalSubmitActionLabel('Add');

        $this->closeModalByClickingAway(false);

        $this->modalDescription('Fill in the details of the new service.');

        $this->form([
                    TableRepeater::make('services')
                        ->addActionLabel('Add Service')
                        ->schema([
                            'name' => TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                        ]),

            ]);

        $this->action(function($data, Service $service){
            foreach($data['services'] as $serviceData) {
                $service->create($serviceData);
            }
        });

        $this->sendSuccessNotification();

        $this->successNotificationTitle('Service added successfully');
    }

}
