<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'POS System' }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Sembunyikan scrollbar tapi tetap bisa scroll (opsional, biar rapi) */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    @livewireStyles
</head>
<body class="bg-gray-100 font-sans antialiased">
    
    {{ $slot }}

    @livewireScripts
</body>
</html>