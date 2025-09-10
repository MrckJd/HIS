<?php

namespace App\Filament\Resources\HouseholdResource\Pages;

use App\Filament\Resources\HouseholdResource;
use App\Filament\Services\PSGCService;
use App\Models\Barangay;
use App\Models\Household;
use App\Models\MemberServices;
use App\Models\Municipality;
use Exception;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateHousehold extends CreateRecord
{
    protected static string $resource = HouseholdResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['address'] = $data['purok'] . ', ' . Barangay::query()->where('code', $data['barangay'])->value('name') . ', ' . Municipality::query()->where('code', $data['municipality'])->value('name');
        return $data;
    }


}
