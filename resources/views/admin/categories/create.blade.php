@extends('admin.layouts.admin')

@section('title', 'Создать категорию')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold">Создать категорию</h1>
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

<form action="{{ route('admin.categories.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
    @csrf

    <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Название *</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}" 
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
               required>
    </div>

    <div class="mb-4">
        <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">URL (slug)</label>
        <input type="text" name="slug" id="slug" value="{{ old('slug') }}" 
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
               placeholder="Автоматически сгенерируется из названия">
    </div>

    <div class="mb-4">
        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Описание</label>
        <textarea name="description" id="description" rows="4" 
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('description') }}</textarea>
    </div>

    <div class="flex space-x-4">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Создать
        </button>
        <a href="{{ route('admin.categories.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
            Отмена
        </a>
    </div>
</form>
@endsection

