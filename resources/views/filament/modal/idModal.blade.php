@php
    $backgroundImageSrc = 'data:image/jpeg;base64,' . base64_encode(file_get_contents(public_path('assets/helpdesk-viewId.jpg')));
    $logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/logo/ddsLOGO-1024x1024.png')));
    if ($member->avatar)
        $idCardSrc = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('storage/' . $member->avatar)));
    else
        if ($member->gender === 'Male')
            $idCardSrc = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/avatars/male-default.jpg')));
        else
            $idCardSrc = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/avatars/female-default.jpg')));

@endphp
    <section class="relative w-[3.15in] h-[2.05in] bg-slate-700 bg-cover p-2 rounded-md" style="background-image: url('{{ $backgroundImageSrc }}');">
        <div class="h-10 text-stone-900 text-center flex flex-col leading-3">
            <span class="font-bold truncate ">Household {{$member?->is_leader ? 'Leader' : 'Member'}}</span>
            <span class="text-[8px] text-stone-900/70">{{$member->is_leader ? '': '('.$member?->household->leader->full_name.')' }}</span>
        </div>
        <div class="flex gap-2 mt-1.5">
            @php
                $address= explode(', ', $member->household?->address);
            @endphp
            <img class="w-[.8in] h-[.8in]" src="{{ $idCardSrc }}" alt="">
            <div class="flex flex-col justify-between bg-stone-800/50 w-full py-1">
                    <div class="flex flex-col text-shadow-lg text-white leading-2">
                        <p class="text-md p-0 font-bold drop-shadow-md  leading-3.5">{{$member?->first_name}} {{substr($member?->middle_name, 0, 1)}}. {{$member?->surname}} <br></p>
                        <span class="text-[7px]">Precinct No.: {{$member->precinct_no}}</span>
                    </div>
                    <div class="text-[10px] leading-2.5 text-amber-600 mt-1">
                        <p>{{ $address[0] ?? '' }},</p>
                        <p>{{ $address[1] ?? '' }},</p>
                        <p>{{ $address[2] ?? '' }}</p>
                    </div>
            </div>
        </div>
        <div class="flex justify-between items-center">
            <div class="absolute bottom-2 right-2 bg-white p-[3px] pb-[1px] rounded-sm shadow-lg">
                {{$member->qrCode}}
                <p class="text-[5px] font-bold text-stone-500 text-center text-shadow-md">{{$member->code}}</p>
            </div>
        </div>
        <div class="absolute pt-0 flex space-x-1 text-[6px] bottom-[1px]">
            <span class="flex text-blue-950 gap-[1px]">
                <x-filament::icon class="w-2" icon='si-facebook'/>
                <span>facebook.com/pictodavsur</span>
            </span>
            <span class="flex text-blue-950 gap-[1px]">
                <x-filament::icon class="w-2" icon='fas-globe'/>
                <span>davaodelsur.gov.ph</span>
            </span>
        </div>
    </section>
