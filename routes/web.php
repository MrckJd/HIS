<?php

use App\Http\Controllers\QrCodeController;
use App\Models\Member;
use Illuminate\Support\Facades\Route;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;

Route::get('/test', function () {
    return view('welcome');
});

Route::get('/test-puppeteer-pdf', function () {
    // return view('filament.MemberID', ['member' => Member::first(), 'qrCode' => QrCode::size(150)->generate('fsfy9067')]);

    try {
        return Pdf::view('filament.MemberID', ['member' => Member::first(), 'qrCode' => QrCode::size(150)->generate('fsfy9067')])
            ->format('a4')
            ->withBrowsershot(fn (Browsershot $bs) => $bs->noSandbox()->setDelay(2000)
                    ->timeout(60)
                    ->showBackground())
            ->name('test.pdf');
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});
