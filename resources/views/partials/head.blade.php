<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

{{-- <title>{{ $title ?? config('app.name') }}</title> --}}
<title>{{ $title }} | Apotek</title>
{{-- <link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png"> --}}
<link rel="shortcut icon" type="image/png" href="{{ asset('/assets/images/logos/favicon.png') }}">

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

@vite(['resources/css/personalization.css', 'resources/css/app.css', 'resources/js/vendor.min.js', 'resources/js/theme/app.min.js', 'resources/js/app.js'])
