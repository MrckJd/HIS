<?php

namespace App\Jobs;

use App\Models\Member;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Facades\Pdf;

class GeneratePDF implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public array $householdIds;
    public string $filename;

    public function __construct(array $householdIds)
    {
        $this->householdIds = $householdIds;
        $this->filename = $filename ?? 'member_ids_' . now()->timestamp . '.pdf';
    }

    public function handle(): void
    {
        $members = Member::whereIn('household_id', $this->householdIds)->get();

        if(storage_path('app/public/PDF') && !file_exists(storage_path('app/public/PDF'))){
            mkdir(storage_path('app/public/PDF'), 0755, true);
        }

        $pdfPath = storage_path('app/public/PDF/' . $this->filename);

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

    }
}
