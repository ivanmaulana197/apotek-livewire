<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title }}</title>

    @vite(['resources/css/theme.css', 'resources/css/app.css'])
</head>

<body class="bg-white p-4 text-sm font-mono text-gray-800">
    {{ $slot }}
</body>
