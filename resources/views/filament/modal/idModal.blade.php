@php
    $backgroundImageSrc = 'data:image/jpeg;base64,' . base64_encode(file_get_contents(public_path('assets/helpdesk-viewId.jpg')));
    $logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/logo/ddsLOGO-1024x1024.png')));
    $signatureSrc = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/gov-signature.png')));

@endphp
    <section class="w-[3.15in] h-[2.05in] bg-slate-700 bg-cover p-2 rounded-md" style="background-image: url('{{ $backgroundImageSrc }}');">
        <div class="flex items-center justify-center space-x-1">
            <img class="w-10" src="{{ $logoSrc }}" alt="DDS Logo">
            <div class="flex flex-col text-left text-[9px] leading-none">
                <span class="text-blue-950">REPUBLIC OF THE PHILIPPINES</span>
                <span class="text-slate-950"><b>PROVINCE OF DAVAO DEL SUR</b></span>
                <span class="text-blue-950">MATTI, DIGOS CITY</span>
            </div>
        </div>
        <div class="mt-2">
            @php
                $address= explode(', ', $member->household?->address);
            @endphp
            <div>
                <div class="text-white leading-2.5">
                    <span class="text-md font-bold drop-shadow-md">{{$member->first_name}} {{substr($member->middle_name, 0, 1)}}. {{$member->surname}}</span><br>
                    <span class="text-[7px]"> {{ $member->household?->title ?? 'na'}}</span> <span class="text-[8px]"> ({{ $member->is_leader ? 'Household Leader' : $member->role }})</span>
                </div>
                <x-bi-person-bounding-box />
            </div>

            <div class="text-[12px] leading-2.5 text-amber-600 mt-2">
                <p>{{ $address[0] ?? '' }}</p>
                <p>{{ $address[1] ?? '' }}</p>
                <p>{{ $address[2] ?? '' }}</p>
            </div>
        </div>
        <div class="flex justify-between items-center mt-5">
            <div class="ml-1 flex flex-col items-center">
                <img class="w-10" src="{{ $signatureSrc }}" alt="gov-signature">
                <div class="text-center text-amber-600 leading-3">
                    <p class="text-md underline">Yvonne R. Cagas</p>
                    <p class="text-[9px] text-blue-950">Provincial Governor</p>
                </div>
            </div>
            <div class="bg-white p-[3px] rounded-sm shadow-lg">
                {{$member->qrCode}}
            </div>
        </div>
        <div class="pt-0 flex space-x-1 text-[6px]">
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
