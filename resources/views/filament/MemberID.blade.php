    @php
    $preview = $preview ?? false;
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
    @endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>


    @if($cssFile ?? 0)
            @if ($preview)
                @vite('resources/css/app.css')
            @else
                <style>
                    {!! file_get_contents(public_path('build/' . $cssFile)) !!}

                    /* @media print{
                            @page {
                                size: A4;
                                margin: 0;
                            }
                    } */
                </style>
            @endif
    @endif
    {{-- <style>
                    @media print{
                            @page {
                                size: A4;
                                place-content: center space-around;
                            }
                    }
                </style> --}}
    @vite('resources/css/app.css')
</head>
<body class="font-roboto p-0 m-0">
    @if ($preview)
        @include('filament.modal.idModal', [
                            'member' => $member,
                        ])
    @else
        @foreach ($members->chunk(10) as $chunked)
            <section class="grid grid-cols-2 gap-4 p-8">
                    @foreach($chunked as $member)

                        @include('filament.modal.idModal', [
                            'member' => $member,
                        ])
                    @endforeach
                    @pageBreak
                </section>
        @endforeach
    @endif
    {{-- @foreach ($members->chunk(10) as $chunked)
            <section class="grid grid-cols-2 gap-2">
                    @foreach($chunked as $member)

                        @include('filament.modal.idModal', [
                            'member' => $member,
                        ])
                    @endforeach
                    @pageBreak
                </section>
        @endforeach --}}
</body>
</html>
