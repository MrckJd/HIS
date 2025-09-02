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

Route::get('/test-row',function(){
    return view('filament.table.collapsible-table-row');
});

Route::get('/test-pdf', function($member = null) {
    $members=Member::take(10)->get();
    return view('filament.MemberID', [
        'leader' => 'test-leader this is',
        'members' => $members,
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
