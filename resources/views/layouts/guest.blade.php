<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WMS PRO') }} - Portal Akses</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    
    {{-- Background Biru Tua (Dark Mode) --}}
    <body class="font-sans antialiased bg-blue-900" style="background-image: linear-gradient(135deg, #0A1930 0%, #172B4D 100%);">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">

            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white overflow-hidden sm:rounded-2xl 
                        shadow-2xl shadow-blue-900/50">
                
                {{-- Logo/Title di dalam Card Login --}}
                <div class="text-center mb-6">
                    <div class="h-10 w-10 mx-auto mb-2 bg-cyan-400 rounded-full flex items-center justify-center text-slate-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-slate-800">Staff Portal</h2>
                    <p class="text-xs text-slate-500 mt-1">Masuk untuk mengakses Dashboard Manager</p>
                </div>
                
                {{ $slot }}
            </div>
        </div>
    </body>
</html>