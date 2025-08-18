@php($preview ??= false)@endphp

    @php
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
    // $backgroundImageSrc = 'data:image/jpeg;base64,' . base64_encode(file_get_contents(public_path('assets/helpdesk-viewId.jpg')));
    // $logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/logo/ddsLOGO-1024x1024.png')));
    @endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>


    {{-- @if($cssFile ?? 0) --}}
    <style>
        {!! file_get_contents(public_path('build/' . $cssFile)) !!}


        /* @media print {
            @page {
                orientation: landscape;
                size: a4;
                print-color-adjust: exact;
                -webkit-print-color-adjust:exact !important;
                print-color-adjust:exact !important;
            }

            body {
            }
        } */
    </style>
    {{-- @endif --}}
    {{-- @vite('resources/css/app.css') --}}
</head>
<body class="font-roboto p-0 m-0">

    @foreach ($members->chunk(4) as $chunked)
        <section class="grid grid-cols-2 gap-4 p-8">
                @foreach($chunked as $member)

                    @include('filament.modal.idModal', [
                        'member' => $member,
                    ])
                @endforeach
                @pageBreak
            </section>
    @endforeach
</body>
</html>
