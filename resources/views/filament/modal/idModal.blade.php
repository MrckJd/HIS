@php
    $backgroundImageSrc = 'data:image/jpeg;base64,' . base64_encode(file_get_contents(public_path('assets/helpdesk-viewId.jpg')));
    $logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/logo/ddsLOGO-1024x1024.png')));
    $signatureSrc = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/gov-signature.png')));
    $idCardSrc = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('storage/' . $member->avatar)));

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
        <div class="flex gap-2 mt-1.5">
            @php
                $address= explode(', ', $member->household?->address);
            @endphp
            <img class="w-[.8in] h-[.8in]" src="{{ $idCardSrc }}" alt="">
            <div>
                    <div class="flex flex-col bg-stone-800/50 p-1 text-shadow-lg text-white">
                        <p class="text-md p-0 font-bold drop-shadow-md  leading-3.5">{{$member->first_name}} {{substr($member->middle_name, 0, 1)}}. {{$member->surname}} <br></p>
                        <span class="text-[8px]"> ({{ $member->is_leader ? 'Household Leader' : $member->role }})</span>
                        @if (!$member->is_leader)
                            <p class="text-[7px] leading-2 text-white mt-1 text-shadow-md">Household Leader: {{$member->household?->leader_name}}</p>
                        @endif
                    </div>

                    <div class="text-[10px] leading-2.5 text-amber-600">
                        <p>{{ $address[0] ?? '' }},</p>
                        <p>{{ $address[1] ?? '' }},</p>
                        <p>{{ $address[2] ?? '' }}</p>
                    </div>
            </div>
        </div>
        <div class="flex justify-between items-center">
            <div class="w-1">
                {{-- <img class="w-10" src="{{ $signatureSrc }}" alt="gov-signature"> --}}
                {{-- <div class="text-center text-amber-600 leading-3">
                    <p class="text-md underline">Yvonne R. Cagas</p>
                    <p class="text-[9px] text-blue-950">Provincial Governor</p>
                </div> --}}
            </div>
            <div class="fixed bottom-4 right-2 bg-white p-[3px] pb-[1px] rounded-sm shadow-lg">
                {{$member->qrCode}}
                <p class="text-[5px] text-stone-500 text-center text-shadow-md">ID Code: {{$member->code}}</p>
            </div>
        </div>
        <div class="fixed pt-0 flex space-x-1 text-[6px] bottompx]">
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
