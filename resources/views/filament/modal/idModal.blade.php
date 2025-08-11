
@if($member)
<section class="bg-white h-96 rounded-lg">
    <div class="flex items-center justify-center space-x-4">
        <img class="w-24" src="{{asset('assets/logo/ddsLOGO-1024x1024.png')}}" alt="DDS Logo">
        <div class="text-center">
            <p class="text-md text-gray-800">Republic of the Philippines</p>
            <p class="text-md text-gray-800">Province of Davao del Sur</p>
            <p class="text-md text-gray-800">Matti, Digos City</p>
        </div>
    </div>
    <br><br>
    <div class="mx-4">
        @php
            $address= explode(', ', $member->household->address);
        @endphp
        <p class="text-md text-gray-800">Name:  {{ $member->first_name }} {{substr($member->middle_name, 0, 1)}}. {{$member->surname}}</p>
        <p class="text-md text-gray-800">Municipality/City: {{ $address[2] }}</p>
        <p class="text-md text-gray-800">Barangay:  {{ $address[1] }}</p>
        <p class="text-md text-gray-800">Purok/Sitio:   {{ $address[0] }}</p>
    </div>
</section>
@endif
