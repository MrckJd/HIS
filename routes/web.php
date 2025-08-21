<?php

use App\Http\Controllers\MemberController;
use App\Http\Controllers\QrCodeController;
use App\Models\Member;
use Illuminate\Support\Facades\Route;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;

Route::get('/test', function () {
    return view('welcome');
});

Route::get('/test-pdf', function($member = null) {
    $member=Member::take(1)->get();
    return view('filament.MemberID', [
        'leader' => dd($member->household->LeaderName),
        'member' => $member,
        'preview'=>true,
    ]);
});

Route::get('/id-preview/{record}', function ($member= null) {
    $member = Member::findOrFail($member);
    return view('filament.MemberID', [
        'leader' => $member->household->LeaderName,
        'member' => $member,
        'qrCode' => QrCode::size(150)->generate($member->code ? $member->code : ' No QR'),
        'preview' => true,

    ]);
});
