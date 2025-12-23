<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full w-full bg-gray-900">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- <link href="/src/style.css" rel="stylesheet"> --}}
    <link href="https://rsms.me/inter/inter.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <title>Halaman Home</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
</head>

<body class="antialiased font-sans">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 relative">
        <div class="sticky top-0 right-0 left-0">
            @include('components.navbar')

            {{-- <x-navbar></x-navbar> --}}

            {{-- <x-header class="bg-white dark:bg-gray-800 shadow">{{ $title }}</x-header> --}}
            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow backdrop-blur-lg">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-10">
                        {{ $header }}
                    </div>
                </header>
            @endisset
        </div>

        <main>
            <div class="mx-auto max-w-8xl px-4 py-4 sm:px-3 lg:px-10 text-white">
                <!-- Your content -->
                {{ $slot }}
            </div>
        </main>
    </div>
    @stack('scripts')
</body>

</html>
