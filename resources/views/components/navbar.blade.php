<nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Логотип -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-indigo-600 hover:text-indigo-700 transition">
                    LearnStream
                </a>
            </div>

            <!-- Навигационные ссылки (скрыты на мобильных) -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-indigo-600 transition font-medium">
                    Главная
                </a>
                <a href="{{ route('faq') }}" class="text-gray-700 hover:text-indigo-600 transition font-medium">
                    FAQ
                </a>
            </div>

            <!-- Кнопки действий -->
            <div class="flex items-center space-x-4">
                @auth
                    @if(auth()->user()->role === \App\Enums\UserRole::Admin)
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-indigo-600 transition font-medium">
                            Админ-панель
                        </a>
                    @elseif(auth()->user()->role === \App\Enums\UserRole::Instructor)
                        <a href="{{ route('instructor.dashboard') }}" class="text-gray-700 hover:text-indigo-600 transition font-medium">
                            Панель инструктора
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-indigo-600 transition font-medium">
                            Дашборд
                        </a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="text-gray-700 hover:text-indigo-600 transition font-medium">
                        Войти
                    </a>
                    <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
                        Регистрация
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

