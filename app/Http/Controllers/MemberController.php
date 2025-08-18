<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;

class MemberController extends Controller
{
    public function generateMemberId(Request $request)
    {
        $memberids = explode(', ',$request->members);
        $members = Member::whereIn('id', $memberids)->get();

        try {
        return Pdf::view('filament.MemberID', ['members' => $members])
            ->format('a4')
            ->withBrowsershot(fn (Browsershot $bs) => $bs
                    ->landscape()
                    ->noSandbox()
                    ->setDelay(2000)
                    ->timeout(60)
                    ->showBackground()
            )
            ->landscape()
            ->name('test.pdf');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
