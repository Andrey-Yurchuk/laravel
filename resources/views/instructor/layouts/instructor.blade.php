<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Панель инструктора') - LearnStream</title>

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicons/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('favicons/site.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white flex flex-col">
            <div class="p-6 flex-1">
                <div class="mb-6">
                    <a href="{{ route('instructor.dashboard') }}" class="text-2xl font-bold text-white mb-2 block">
                        Панель инструктора
                    </a>
                    @auth
                    <div class="text-sm text-gray-300 mb-4">
                        {{ Auth::user()->name }}
                    </div>
                    @endauth
                </div>
                <nav class="space-y-2">
                    <a href="{{ route('instructor.dashboard') }}" class="block px-4 py-2 hover:bg-gray-700 rounded-lg transition">
                        Дашборд
                    </a>
                    <a href="{{ route('instructor.courses.index') }}" class="block px-4 py-2 hover:bg-gray-700 rounded-lg transition">
                        Мои курсы
                    </a>
                </nav>
            </div>
            <div class="p-6 border-t border-gray-700">
                <a href="{{ route('home') }}" class="block px-4 py-2 hover:bg-gray-700 rounded-lg transition mb-2">
                    На главную
                </a>
                @auth
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-700 rounded-lg transition">
                        Выйти
                    </button>
                </form>
                @endauth
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @livewireScripts
</body>
</html>

