<div>
    @if (session()->has('contact_message'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('contact_message') }}
        </div>
    @endif

    <form wire:submit="submit">
        <div class="mt-0">
            <label for="contact_name" class="block text-sm font-medium text-gray-700 mb-2">
                Ваше имя
            </label>
            <input 
                type="text" 
                id="contact_name"
                wire:model="name"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-500 @enderror"
                placeholder="Иван Иванов"
            >
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-8">
            <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                Email
            </label>
            <input 
                type="email" 
                id="contact_email"
                wire:model="email"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-red-500 @enderror"
                placeholder="ivan@example.com"
            >
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-8">
            <label for="contact_message" class="block text-sm font-medium text-gray-700 mb-2">
                Ваше сообщение
            </label>
            <textarea 
                id="contact_message"
                wire:model="message"
                rows="5"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('message') border-red-500 @enderror"
                placeholder="Напишите ваше сообщение..."
            ></textarea>
            @error('message')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-8">
            <button 
                type="submit"
                class="w-full bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 cursor-pointer"
            >
                Отправить сообщение
            </button>
        </div>
    </form>
</div>
