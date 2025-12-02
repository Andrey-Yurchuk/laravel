@extends('instructor.layouts.instructor')

@section('title', 'Создать курс')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold">Создать курс</h1>
</div>

@if($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('instructor.courses.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
    @csrf

    <div class="mb-4">
        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Название *</label>
        <input type="text" name="title" id="title" value="{{ old('title') }}" 
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
               required>
    </div>

    <div class="mb-4">
        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Категория *</label>
        <select name="category_id" id="category_id" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                required>
            <option value="">Выберите категорию</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Описание *</label>
        <textarea name="description" id="description" rows="4" 
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                  required>{{ old('description') }}</textarea>
    </div>

    <div class="mb-4">
        <label for="difficulty_level" class="block text-sm font-medium text-gray-700 mb-2">Уровень сложности *</label>
        <select name="difficulty_level" id="difficulty_level" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                required>
            <option value="beginner" {{ old('difficulty_level') == 'beginner' ? 'selected' : '' }}>Начинающий</option>
            <option value="intermediate" {{ old('difficulty_level') == 'intermediate' ? 'selected' : '' }}>Средний</option>
            <option value="advanced" {{ old('difficulty_level') == 'advanced' ? 'selected' : '' }}>Продвинутый</option>
        </select>
    </div>

    <div class="mb-4">
        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Статус *</label>
        <select name="status" id="status" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                required>
            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Черновик</option>
            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Опубликован</option>
            <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Архивирован</option>
        </select>
    </div>

    <div class="flex space-x-4">
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
            Создать
        </button>
        <a href="{{ route('instructor.courses.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
            Отмена
        </a>
    </div>
</form>
@endsection

