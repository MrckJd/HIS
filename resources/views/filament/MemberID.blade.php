@php
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
    $imageData = base64_encode(file_get_contents(public_path('assets/helpdesk-viewId.jpg')));
    $imageSrc = 'data:image/jpeg;base64,' . $imageData;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>

    @if($cssFile)
    <style>
        {!! file_get_contents(public_path('build/' . $cssFile)) !!}
    </style>
    @endif
</head>
<body class="flex">
    <section style="background-image: url('{{ $imageSrc }}');" class="bg-contain bg-center h-[639px] w-[1015px] rounded-lg p-8 font-roboto">
        <h1 style="color: Black; font-size: 2rem; font-weight: bold;">{{ $member->first_name }} {{ $member->middle_name }} {{ $member->surname }}</h1>
        <div style="background: black; padding: 1rem; border-radius: 8px; display: inline-block; margin-top: 1rem;">
            {!! $qrCode !!}
        </div>
    </section>
    <section style="background-image: url('{{ $imageSrc }}');">
        <h1 style="color: Black; font-size: 2rem; font-weight: bold;">{{ $member->first_name }} {{ $member->middle_name }} {{ $member->surname }}</h1>
        <div style="background: black; padding: 1rem; border-radius: 8px; display: inline-block; margin-top: 1rem;">
            {!! $qrCode !!}
        </div>
    </section>
</body>
</html>
