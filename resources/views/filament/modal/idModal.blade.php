
@if($member)
<section class="bg-[url('{{asset('assets/helpdesk-viewId.jpg')}}')] bg-contain bg-center h-[639px] w-[1015px] rounded-lg p-8 font-roboto">
    <div class="flex items-center justify-center space-x-4">
        <img class="w-32" src="{{asset('assets/logo/ddsLOGO-1024x1024.png')}}" alt="DDS Logo">
        <div class="flex flex-col text-left text-2xl leading-6">
            <span class="text-md text-blue-950">REPUBLIC OF THE PHILIPPINES</span>
            <span class="text-md text-slate-950"><b>PROVINCE OF DAVAO DEL SUR</b></span>
            <span class="text-md text-blue-950">MATTI, DIGOS CITY</span>
        </div>
    </div>
    <br><br>
    <div>
        @php
            $address= explode(', ', $member->household->address);
        @endphp
        <span class="text-5xl text-white  font-bold drop-shadow-md">{{$member->first_name}} {{substr($member->middle_name, 0, 1)}}. {{$member->surname}}</span><br>
        <span> {{ $member->household->title}}</span> <span> ({{ $member->is_leader ? 'Household Leader' : $member->role }})</span>
        <br><br>
        <div class="text-4xl leading-8 font-bold text-amber-600">
            <p>{{ $address[0] }}</p>
            <p>{{ $address[1] }}</p>
            <p>{{ $address[2] }}</p>
        </div>
    </div>
    <br>
    <div class="flex justify-between items-center">
        <div class="ml-8 flex flex-col items-center">
            <br>
            <img src="{{ asset('assets/gov-signature.png') }}" alt="gov-signature">
            <div class="leading-1 text-center text-amber-600">
                <p class="text-4xl">YVONNE R. CAGAS</p>
                <p class="text-blue-950">PROVINCIAL GOVERNOR</p>
            </div>
        </div>
        <div class="bg-white p-2 rounded-md shadow-lg">
            {{$qrCode}}
        </div>
    </div>
    <div class="pt-4 flex space-x-8">
        <span class="flex space-x-2 text-blue-950">
            <x-filament::icon class="w-6" icon='si-facebook'/>
            <span>facebook.com/pictodavsur</span>
        </span>
        <span class="flex space-x-2 text-blue-950">
            <x-filament::icon class="w-6" icon='fas-globe'/>
            <span>davaodelsur.gov.ph</span>
        </span>
    </div>
</section>
@endif
