    @php
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
    $preview = $preview ?? false;
    @endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Generated ID</title>


    @if($cssFile ?? 0)
            @if ($preview)
                @vite('resources/css/app.css')
            @else
                <style>
                    {!! file_get_contents(public_path('build/' . $cssFile)) !!}
                </style>
            @endif
    @endif
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
</body>
</html>
