<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Kostly') }} - SaaS Manajemen Kost & Penagihan Otomatis</title>
    <meta name="description" content="Aplikasi manajemen properti kost modern. Otomatisasi tagihan, pengingat WhatsApp, pembayaran QRIS, dan portal mandiri anak kost.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-50 font-sans text-slate-800 antialiased selection:bg-indigo-600 selection:text-white">
    {{ $slot }}
</body>
</html>

