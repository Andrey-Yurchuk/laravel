<div>
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit="submit">
        <!-- Имя -->
        <div class="mt-0">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Имя
            </label>
            <input 
                type="text" 
                id="name"
                wire:model="name"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-500 @enderror"
                placeholder="Иван Иванов"
            >
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div class="mt-8">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Email
            </label>
            <input 
                type="email" 
                id="email"
                wire:model="email"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-red-500 @enderror"
                placeholder="ivan@example.com"
            >
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Пароль -->
        <div class="mt-8">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                Пароль
            </label>
            <input 
                type="password" 
                id="password"
                wire:model="password"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('password') border-red-500 @enderror"
                placeholder="Минимум 8 символов"
            >
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @if($password)
                <p class="mt-1 text-xs text-gray-500">
                    Сила пароля: 
                    <span class="font-medium {{ strlen($password) >= 8 ? 'text-green-600' : 'text-yellow-600' }}">
                        {{ strlen($password) >= 8 ? 'Хороший' : 'Слабый' }}
                    </span>
                </p>
            @endif
        </div>

        <!-- Подтверждение пароля -->
        <div class="mt-8">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                Подтверждение пароля
            </label>
            <input 
                type="password" 
                id="password_confirmation"
                wire:model="password_confirmation"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('password_confirmation') border-red-500 @enderror"
                placeholder="Повторите пароль"
            >
            @error('password_confirmation')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Роль -->
        <div class="mt-8">
            <label class="block text-sm font-medium text-gray-700 mb-4">
                Роль
            </label>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input 
                        type="radio" 
                        wire:model="role" 
                        value="student"
                        class="mr-2 text-indigo-600 focus:ring-indigo-500"
                    >
                    <span class="text-gray-700">Студент (хочу обучаться)</span>
                </label>
                <label class="flex items-center">
                    <input 
                        type="radio" 
                        wire:model="role" 
                        value="instructor"
                        class="mr-2 text-indigo-600 focus:ring-indigo-500"
                    >
                    <span class="text-gray-700">Преподаватель (хочу создавать курсы)</span>
                </label>
            </div>
            @error('role')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Согласие с условиями -->
        <div class="mb-6 mt-8">
            <label class="flex items-start">
                <input 
                    type="checkbox" 
                    wire:model="terms"
                    class="mt-1 mr-2 text-indigo-600 focus:ring-indigo-500 rounded"
                >
                <span class="text-sm text-gray-700">
                    Я согласен с <a href="#" class="text-indigo-600 hover:underline">условиями использования</a> и <a href="#" class="text-indigo-600 hover:underline">политикой конфиденциальности</a>
                </span>
            </label>
            @error('terms')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Кнопка отправки -->
        <button 
            type="submit"
            class="w-full bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 cursor-pointer mt-8"
        >
            Создать аккаунт
        </button>
    </form>
</div>
