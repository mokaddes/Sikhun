<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!--
        Inline theme script — runs before Vue mounts to prevent a flash
        of the wrong theme (light) when the student has dark mode saved.
    -->
    <script>
        (function () {
            var mode = localStorage.getItem('sikhun_theme') || 'system';
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (mode === 'dark' || (mode === 'system' && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <title inertia>{{ app(\App\Services\SiteSettingService::class)->get('site_name', config('app.name', 'Sikhun.com')) }}</title>

    @php
        $favicon = app(\App\Services\SiteSettingService::class)->get('site_favicon');
    @endphp
    @if($favicon)
        <link rel="icon" href="{{ asset('storage/'.$favicon) }}">
        <link rel="apple-touch-icon" href="{{ asset('storage/'.$favicon) }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">

    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="font-sans antialiased">
    @inertia
</body>
</html>
