<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Performance optimizations -->
        <link rel="dns-prefetch" href="//fonts.bunny.net">
        <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>

        <!-- Preload critical fonts -->
        <link rel="preload" href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
        <noscript><link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"></noscript>

        <!-- Meta tags for better SEO and performance -->
        <meta name="description" content="Mickyes Coiffure - Professional barbering services in Newcastle. Book your appointment online for haircuts, beard grooming, and more.">
        <meta name="theme-color" content="#16a34a">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js'])
        @inertiaHead
        @if (!empty($page['props']['jsonLd']))
            <script type="application/ld+json">{{ $page['props']['jsonLd'] }}</script>
        @endif
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
