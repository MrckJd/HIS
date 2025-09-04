<?php

namespace App\Jobs;

use App\Models\Member;
use Croustibat\FilamentJobsMonitor\Traits\QueueProgress;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Facades\Pdf;

class GeneratePDF implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, QueueProgress;

    public array $householdIds;
    public string $filename;

    public function __construct(array $householdIds, string $filename)
    {
        $this->householdIds = $householdIds;
        $this->filename = $filename ?? 'member_ids_' . now()->timestamp . '.pdf';
    }

    public function handle(): void
    {
        $this->setProgress(0);
        $members = Member::whereIn('household_id', $this->householdIds)->get();

        if(storage_path('app/public/PDF') && !file_exists(storage_path('app/public/PDF'))){
            mkdir(storage_path('app/public/PDF'), 0755, true);
        }
        sleep(1);

        $this->setProgress(mt_rand(10,20));

        $pdfPath = storage_path('app/public/PDF/' . $this->filename);

        sleep(1);

        $this->setProgress(mt_rand(40,50));

        Pdf::view('filament.MemberID', ['members' => $members])
            ->format(Format::A4)
            ->withBrowsershot(fn (Browsershot $bs) => $bs
                ->portrait()
                ->noSandbox()
                ->setDelay(2000)
                ->timeout(60)
                ->showBackground()
            )
            ->save($pdfPath);

        sleep(1);

        $this->setProgress(100);

    }

    public function displayName(): string
    {
        return $this->filename;
    }
}
