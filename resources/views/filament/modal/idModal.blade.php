@php
    $backgroundImageSrc = 'data:image/jpeg;base64,' . base64_encode(file_get_contents(public_path('assets/helpdesk-viewId.jpg')));
    $logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/logo/ddsLOGO-1024x1024.png')));
    $signatureSrc = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/gov-signature.png')));

@endphp
    <section class="w-[4.25in] h-[3in] bg-slate-700 bg-cover p-4 rounded-md" style="background-image: url('{{ $backgroundImageSrc }}');">
        <div class="flex items-center justify-center space-x-1">
            <img class="w-14" src="{{ $logoSrc }}" alt="DDS Logo">
            <div class="flex flex-col text-left text-[12px] leading-3">
                <span class="text-blue-950">REPUBLIC OF THE PHILIPPINES</span>
                <span class="text-slate-950"><b>PROVINCE OF DAVAO DEL SUR</b></span>
                <span class="text-blue-950">MATTI, DIGOS CITY</span>
            </div>
        </div>
        <div class="mt-6">
            @php
                $address= explode(', ', $member->household?->address);
            @endphp
            <div class="text-white leading-[1px]">
                <span class="text-xl font-bold drop-shadow-md">{{$member->first_name}} {{substr($member->middle_name, 0, 1)}}. {{$member->surname}}</span><br>
                <span class="text-[9px]"> {{ $member->household?->title ?? 'na'}}</span> <span class="text-[8px]"> ({{ $member->is_leader ? 'Household Leader' : $member->role }})</span>
            </div>
            <div class="text-md leading-4 text-amber-600 mt-4">
                <p>{{ $address[0] ?? '' }}</p>
                <p>{{ $address[1] ?? '' }}</p>
                <p>{{ $address[2] ?? '' }}</p>
            </div>
        </div>
        <div class="flex justify-between items-center mt-[15pt]">
            <div class="ml-1 flex flex-col items-center leading-1">
                <img class="w-12" src="{{ $signatureSrc }}" alt="gov-signature">
                <div class="text-center text-amber-600">
                    <p class="text-lg underline">Yvonne R. Cagas</p>
                    <p class="text-[10px] text-blue-950">Provincial Governor</p>
                </div>
            </div>
            <div class="bg-white p-1 rounded-sm shadow-lg">
                {{$member->qrCode}}
            </div>
        </div>
        <div class="pt-1 flex space-x-1 text-[8px]">
            <span class="flex text-blue-950">
                <x-filament::icon class="w-2" icon='si-facebook'/>
                <span>facebook.com/pictodavsur</span>
            </span>
            <span class="flex text-blue-950">
                <x-filament::icon class="w-2" icon='fas-globe'/>
                <span>davaodelsur.gov.ph</span>
            </span>
        </div>
    </section>
