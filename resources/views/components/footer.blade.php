<footer class="bg-gray-800 text-gray-300 mt-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- О платформе -->
            <div>
                <h3 class="text-white text-lg font-semibold mb-4">LearnStream</h3>
                <p class="text-sm">
                    Платформа онлайн-образования с системой подписок на курсы. 
                    Изучайте новое в удобном темпе.
                </p>
            </div>

            <!-- Ссылки -->
            <div>
                <h4 class="text-white font-semibold mb-4">Навигация</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-white transition">Главная</a></li>
                    <li><a href="{{ route('faq') }}" class="hover:text-white transition">FAQ</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-white transition">Регистрация</a></li>
                </ul>
            </div>

            <!-- Поддержка -->
            <div>
                <h4 class="text-white font-semibold mb-4">Поддержка</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('faq') }}" class="hover:text-white transition">Часто задаваемые вопросы</a></li>
                    <li><a href="#" class="hover:text-white transition">Контакты</a></li>
                    <li><a href="#" class="hover:text-white transition">Помощь</a></li>
                </ul>
            </div>

            <!-- Контакты -->
            <div>
                <h4 class="text-white font-semibold mb-4">Контакты</h4>
                <ul class="space-y-2 text-sm">
                    <li>Email: info@learnstream.com</li>
                    <li>Телефон: +7 (999) 123-45-67</li>
                </ul>
            </div>
        </div>

        <!-- Копирайт -->
        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm">
            <p>&copy; {{ date('Y') }} LearnStream. Все права защищены.</p>
        </div>
    </div>
</footer>

