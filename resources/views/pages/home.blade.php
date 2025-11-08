@extends('layouts.guest')

@section('title', 'Главная - LearnStream')

@section('content')
    <!-- Hero секция -->
    <section class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-12 md:py-20 lg:py-24">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">
                    Изучайте новое с LearnStream
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-indigo-100">
                    Доступ к тысячам курсов по программированию, дизайну и маркетингу
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition text-lg">
                        Начать обучение
                    </a>
                    <a href="#" class="bg-indigo-700 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-800 transition text-lg border-2 border-white">
                        Стать преподавателем
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Популярные курсы -->
    <section class="py-12 md:py-16 lg:py-20 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-8 md:mb-12 text-gray-800">
                Популярные курсы
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @for($i = 1; $i <= 6; $i++)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <div class="h-48 bg-gradient-to-br from-indigo-400 to-purple-500"></div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2 text-gray-800">Курс {{ $i }}</h3>
                        <p class="text-gray-600 mb-4">Преподаватель {{ $i }}</p>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <span class="text-yellow-400">★★★★★</span>
                                <span class="ml-2 text-gray-600">4.8</span>
                            </div>
                            <span class="text-gray-600">{{ 100 + $i * 50 }} студентов</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-indigo-600">${{ 19 + $i * 5 }}</span>
                            <a href="#" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                                Подробнее
                            </a>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </section>

    <!-- Преимущества платформы -->
    <section class="py-12 md:py-16 lg:py-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-8 md:mb-12 text-gray-800">
                Почему выбирают LearnStream?
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="text-5xl mb-4">📚</div>
                    <h3 class="text-xl font-semibold mb-2 text-gray-800">Тысячи курсов</h3>
                    <p class="text-gray-600">Доступ к тысячам курсов от экспертов в различных областях</p>
                </div>
                <div class="text-center">
                    <div class="text-5xl mb-4">💳</div>
                    <h3 class="text-xl font-semibold mb-2 text-gray-800">Гибкие планы</h3>
                    <p class="text-gray-600">Гибкие планы подписки на любой бюджет</p>
                </div>
                <div class="text-center">
                    <div class="text-5xl mb-4">🎓</div>
                    <h3 class="text-xl font-semibold mb-2 text-gray-800">Сертификаты</h3>
                    <p class="text-gray-600">Получайте сертификаты по завершении курсов</p>
                </div>
                <div class="text-center">
                    <div class="text-5xl mb-4">👨‍🏫</div>
                    <h3 class="text-xl font-semibold mb-2 text-gray-800">Обратная связь</h3>
                    <p class="text-gray-600">Обратная связь от преподавателей и экспертов</p>
                </div>
                <div class="text-center">
                    <div class="text-5xl mb-4">⏱️</div>
                    <h3 class="text-xl font-semibold mb-2 text-gray-800">Свой темп</h3>
                    <p class="text-gray-600">Обучение в своем темпе, без ограничений</p>
                </div>
                <div class="text-center">
                    <div class="text-5xl mb-4">📱</div>
                    <h3 class="text-xl font-semibold mb-2 text-gray-800">Доступ везде</h3>
                    <p class="text-gray-600">Учитесь на любом устройстве, где удобно</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Статистика -->
    <section class="py-12 md:py-16 lg:py-20 bg-indigo-600 text-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div>
                    <div class="text-5xl md:text-6xl font-bold mb-2">50,000+</div>
                    <div class="text-xl text-indigo-200">Студентов</div>
                </div>
                <div>
                    <div class="text-5xl md:text-6xl font-bold mb-2">1,000+</div>
                    <div class="text-xl text-indigo-200">Курсов</div>
                </div>
                <div>
                    <div class="text-5xl md:text-6xl font-bold mb-2">500+</div>
                    <div class="text-xl text-indigo-200">Преподавателей</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Отзывы студентов -->
    <section class="py-12 md:py-16 lg:py-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-8 md:mb-12 text-gray-800">
                Отзывы студентов
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                @for($i = 1; $i <= 3; $i++)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                            {{ $i }}
                        </div>
                        <div class="ml-4">
                            <div class="font-semibold text-gray-800">Студент {{ $i }}</div>
                            <div class="text-yellow-400 text-sm">★★★★★</div>
                        </div>
                    </div>
                    <p class="text-gray-600">
                        "Отличная платформа! Курсы качественные, преподаватели опытные. 
                        Очень доволен результатами обучения."
                    </p>
                </div>
                @endfor
            </div>
        </div>
    </section>
@endsection

