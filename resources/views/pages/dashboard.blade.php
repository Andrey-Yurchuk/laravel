@extends('layouts.app')

@section('title', 'Дашборд - LearnStream')

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Заголовок -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Мой дашборд</h1>
            <p class="text-gray-600 mt-2">Добро пожаловать в ваш личный кабинет</p>
        </div>

        <!-- Активные курсы -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Активные курсы</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @for($i = 1; $i <= 3; $i++)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="h-32 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-lg mb-4"></div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Курс {{ $i }}</h3>
                    <div class="mb-4">
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>Прогресс</span>
                            <span>{{ 20 + $i * 15 }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ 20 + $i * 15 }}%"></div>
                        </div>
                    </div>
                    <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium text-sm">
                        Продолжить обучение →
                    </a>
                </div>
                @endfor
            </div>
        </section>

        <!-- Активные подписки -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Активные подписки</h2>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Premium Access</h3>
                        <p class="text-gray-600 text-sm">Следующий платеж: 15 декабря 2025</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-2xl font-bold text-indigo-600">$29.99</span>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                            Активна
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- История платежей -->
        <section>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">История платежей</h2>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Дата</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Описание</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Сумма</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @for($i = 1; $i <= 5; $i++)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ date('d.m.Y', strtotime("-{$i} days")) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    Подписка Premium Access
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    $29.99
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                        Успешно
                                    </span>
                                </td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection

