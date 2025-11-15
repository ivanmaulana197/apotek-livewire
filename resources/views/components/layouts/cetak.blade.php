<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title }}</title>

    @vite(['resources/css/personalization.css', 'resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print {
                display: none;
            }

            body {
                /* background: white !important; */
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
                zoom: 1 !important;
                transform: none !important;
            }

            .shadow,
            .shadow-sm,
            .shadow-md,
            .shadow-lg {
                box-shadow: none !important;
            }

            /* Hilangkan scroll */
            .overflow-hidden,
            .overflow-auto,
            .overflow-x-auto {
                overflow: visible !important;
            }

            /* Pastikan tabel pas satu halaman */
            table {
                width: 100% !important;
                table-layout: fixed !important;
                border-collapse: collapse !important;
            }

            th,
            td {
                word-wrap: break-word;
                white-space: normal !important;
            }

            /* Hilangkan margin layout yang bikin meluber */
            .print-container {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 10px !important;
            }

            /* Ukuran halaman ke A4 */
            /* @page {
                size: A4;
                margin: 10mm;
            } */
            /* Tombol dan elemen non-print hilang */
            .no-print {
                display: none !important;
            }

            /* Pastikan tidak ada scroll horizontal */
            html,
            body {
                overflow: visible !important;
            }

            /* Pulihkan border radius */
            .rounded,
            .rounded-lg,
            .rounded-xl,
            .rounded-2xl {
                border-radius: 0.75rem !important;
                overflow: hidden !important;
                background-clip: padding-box !important;
            }

            /* Kalau tabel atau card pakai shadow, hilangkan tapi pertahankan radius */
            .shadow,
            .shadow-md,
            .shadow-lg {
                box-shadow: none !important;
            }

            /* Jika card utama kamu pakai overflow-hidden di layout normal */
            .card,
            .print-box {
                overflow: hidden !important;
                border-radius: 12px !important;
                background-clip: padding-box !important;
            }
        }
    </style>
</head>

<body {{ $attributes->merge(['class' => 'p-4 text-sm font-sans text-dark']) }}>
    {{ $slot }}
</body>
