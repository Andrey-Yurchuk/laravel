@extends('layouts.guest')

@section('title', 'Часто задаваемые вопросы - LearnStream')

@section('content')
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-4xl font-bold text-gray-800 mb-8 text-center">Часто задаваемые вопросы</h1>

            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-3">Что такое LearnStream?</h2>
                    <p class="text-gray-600">
                        LearnStream — это платформа онлайн-образования, которая предоставляет доступ к тысячам курсов 
                        по программированию, дизайну и маркетингу. Вы можете учиться в своем темпе и получать 
                        сертификаты по завершении курсов.
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-3">Как начать обучение?</h2>
                    <p class="text-gray-600">
                        Для начала обучения необходимо зарегистрироваться на платформе, выбрать подходящий план подписки 
                        и начать проходить курсы. Вы можете учиться в любое время и с любого устройства.
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-3">Получу ли я сертификат после прохождения курса?</h2>
                    <p class="text-gray-600">
                        Да, после успешного завершения курса вы получите сертификат, который можно добавить в свое 
                        резюме или профиль LinkedIn.
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-3">Можно ли отменить подписку?</h2>
                    <p class="text-gray-600">
                        Да, вы можете отменить подписку в любое время в настройках вашего аккаунта. Доступ к курсам 
                        сохранится до конца оплаченного периода.
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-3">Как связаться с поддержкой?</h2>
                    <p class="text-gray-600">
                        Вы можете связаться с нашей службой поддержки по email: info@learnstream.com. 
                        Мы отвечаем на все запросы в течение 24 часов.
                    </p>
                </div>
            </div>

            <div class="mt-16">
                <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Свяжитесь с нами</h2>
                <div class="bg-white rounded-lg shadow-md p-6 md:p-8">
                    @livewire('contact-form')
                </div>
            </div>
        </div>
    </div>
@endsection

