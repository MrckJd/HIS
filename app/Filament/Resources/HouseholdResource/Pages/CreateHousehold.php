<?php

namespace App\Filament\Resources\HouseholdResource\Pages;

use App\Filament\Resources\HouseholdResource;
use App\Filament\Services\PSGCService;
use App\Models\Household;
use App\Models\MemberServices;
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

    public function handleRecordCreation($data): Model
    {

            DB::beginTransaction();

        try {
            $household = Household::create($data);

            if (isset($data['members'])) {
                foreach ($data['members'] as $memberData) {
                    $memberData['household_id'] = $household->id;
                    $member = $household->members()->create($memberData);

                    if (isset($memberData['memberServices'])) {
                        foreach ($memberData['memberServices'] as $service) {
                            MemberServices::create([
                                'member_id' => $member->id,
                                'service_id' => $service['service_id'],
                                'date_received' => $service['date_received'],
                            ]);
                        }
                    }
                }
            }
            DB::commit();
            Notification::make()
                ->title('Household created successfully.')
                ->success()
                ->send();
            return $household;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }


    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['address'] = $data['purok'] . ', ' . PSGCService::getBarangayName($data['baranggay'], $data['municipality']) . ', ' . PSGCService::getMunicipalityName($data['municipality']);
        return $data;
    }


}
