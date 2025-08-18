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

Route::get('/members-ID',[MemberController::class,'generateMemberId'])->name('members.id');


Route::get('/id-preview/{record}', function (Member $member) {
    return view('filament.modal.idModal', [
        'member' => $member,
        'qrCode' => QrCode::size(150)->generate($member->code ? $member->code : ' No QR')
    ]);
});
