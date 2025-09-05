<?php

namespace App\Jobs;

use App\Models\Municipality;
use App\Services\PSGCService;
use Croustibat\FilamentJobsMonitor\Traits\QueueProgress;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncAddress implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, QueueProgress;

    private ?int $progress = 0;
    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try{
            $this->setProgress($this->progress);

            $municipalities = PSGCService::getMunicipalities();


            $this->progress = 10;

            $this->setProgress($this->progress);

            sleep(2);

            if(empty($municipalities)) {
                return;
            }

            $allocatedProgressPerMunicipality = (int) round(90 / count($municipalities));

            foreach ($municipalities as $municipality) {
                $municipality_id = Municipality::create([
                    'code' => $municipality['code'],
                    'name' => $municipality['name'],
                    'oldName' => $municipality['oldName'] ?? null,
                    'isCapital' => $municipality['isCapital'] ?? false,
                    'isCity' => $municipality['isCity'] ?? false,
                    'isMunicipality' => $municipality['isMunicipality'] ?? false,
                    'districtCode' => $municipality['districtCode'] ?? null,
                    'provinceCode' => $municipality['provinceCode'] ?? null,
                    'regionCode' => $municipality['regionCode'] ?? null,
                    'islandGroupCode' => $municipality['islandGroupCode'] ?? null,
                ]);

                $barangays = PSGCService::getBarangays($municipality['code']);

                if(!empty($barangays)) {

                    foreach ($barangays as $barangay) {
                        $municipality_id->barangays()->create([
                            'code' => $barangay['code'],
                            'name' => $barangay['name'],
                            'oldName' => $barangay['oldName'] ?? null,
                            'isUrban' => $barangay['isUrban'] ?? false,
                            'municipalityCode' => $barangay['municipalityCode'] ?? null,
                            'provinceCode' => $barangay['provinceCode'] ?? null,
                            'regionCode' => $barangay['regionCode'] ?? null,
                            'islandGroupCode' => $barangay['islandGroupCode'] ?? null,
                        ]);

                    }
                }
                $this->progress += $allocatedProgressPerMunicipality;

                $this->setProgress($this->progress);
                sleep(2);
            }
        }catch (\Exception $e) {
            Log::error('Error syncing address data: ' . $e->getMessage());

        }

    }

    public function displayName(): string
    {
        return 'SyncAddress';
    }
}
