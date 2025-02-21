<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <meta name="description" content="Your site description here">
    <meta name="keywords" content="your, keywords, here">
    <meta name="author" content="Your Name">

    <!-- Open Graph Meta Tags (for social sharing) -->
    <meta property="og:title" content="Your Site Title">
    <meta property="og:description" content="Your site description here">
    <meta property="og:image" content="{{ asset('path/to/your/image.jpg') }}">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Your Site Title">
    <meta name="twitter:description" content="Your site description here">
    <meta name="twitter:image" content="{{ asset('path/to/your/image.jpg') }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        @yield('content')
    </div>
</body>
</html>
