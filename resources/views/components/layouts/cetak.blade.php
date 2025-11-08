<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title }}</title>

    @vite(['resources/css/personalization.css', 'resources/css/app.css', 'resources/js/app-cetak.js'])
    <style>
        @media print {
            .no-print {
                display: none;
            }

            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body {{ $attributes->merge(['class' => 'p-4 text-sm font-sans text-dark']) }}>
    {{ $slot }}
</body>
