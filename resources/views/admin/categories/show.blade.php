@extends('admin.layouts.admin')

@section('title', 'Просмотр категории')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold">Просмотр категории</h1>
    <div class="space-x-2">
        <a href="{{ route('admin.categories.edit', $category) }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
            Редактировать
        </a>
        <a href="{{ route('admin.categories.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
            Назад к списку
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <dl class="grid grid-cols-1 gap-4">
        <div>
            <dt class="text-sm font-medium text-gray-500">ID</dt>
            <dd class="mt-1 text-lg text-gray-900">{{ $category->id }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Название</dt>
            <dd class="mt-1 text-lg text-gray-900">{{ $category->name }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">URL (slug)</dt>
            <dd class="mt-1 text-lg text-gray-500">{{ $category->slug }}</dd>
        </div>
        @if($category->description)
        <div>
            <dt class="text-sm font-medium text-gray-500">Описание</dt>
            <dd class="mt-1 text-gray-900">{{ $category->description }}</dd>
        </div>
        @endif
        <div>
            <dt class="text-sm font-medium text-gray-500">Создано</dt>
            <dd class="mt-1 text-gray-900">{{ $category->created_at->format('d.m.Y H:i') }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Обновлено</dt>
            <dd class="mt-1 text-gray-900">{{ $category->updated_at->format('d.m.Y H:i') }}</dd>
        </div>
    </dl>
</div>
@endsection

