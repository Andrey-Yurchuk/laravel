<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'LearnStream - Платформа онлайн-образования')</title>

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
    <div class="flex flex-col md:flex-row min-h-screen">
        <aside class="hidden md:block w-64 bg-white border-r border-gray-200">
            <div class="p-6">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-indigo-600 mb-8 block">
                    LearnStream
                </a>
                <nav class="space-y-2">
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition">
                        Мой профиль
                    </a>
                    <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition">
                        Мои курсы
                    </a>
                    <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition">
                        Мои подписки
                    </a>
                    <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition">
                        История платежей
                    </a>
                    <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition">
                        Сертификаты
                    </a>
                    <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition">
                        Настройки
                    </a>
                </nav>
            </div>
        </aside>

        <div class="flex-1 flex flex-col">
            <header class="bg-white border-b border-gray-200 px-4 py-4 md:px-6">
                <div class="flex items-center justify-between">
                    <button class="md:hidden text-gray-600 hover:text-gray-900">
                        ☰
                    </button>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">Главная</a>
                        <span class="text-gray-300">|</span>
                        <span class="text-gray-900 font-medium">Дашборд</span>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 md:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>

