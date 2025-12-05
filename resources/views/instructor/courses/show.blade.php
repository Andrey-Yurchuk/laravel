@extends('instructor.layouts.instructor')

@section('title', 'Просмотр курса')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold">Просмотр курса</h1>
    <div class="space-x-2">
        <a href="{{ route('instructor.courses.edit', $course) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
            Редактировать
        </a>
        <a href="{{ route('instructor.courses.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
            Назад к списку
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <dl class="grid grid-cols-1 gap-4">
        <div>
            <dt class="text-sm font-medium text-gray-500">ID</dt>
            <dd class="mt-1 text-lg text-gray-900">{{ $course->id }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Название</dt>
            <dd class="mt-1 text-lg text-gray-900">{{ $course->title }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Категория</dt>
            <dd class="mt-1 text-gray-900">{{ $course->category->name }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Описание</dt>
            <dd class="mt-1 text-gray-900">{{ $course->description }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Уровень сложности</dt>
            <dd class="mt-1 text-gray-900">{{ $course->difficulty_level->value }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Статус</dt>
            <dd class="mt-1">
                <span class="px-2 py-1 text-xs rounded-full 
                    @if($course->status->value === 'published') bg-green-100 text-green-800
                    @elseif($course->status->value === 'draft') bg-yellow-100 text-yellow-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ $course->status->value }}
                </span>
            </dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Создано</dt>
            <dd class="mt-1 text-gray-900">{{ $course->created_at->format('d.m.Y H:i') }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Обновлено</dt>
            <dd class="mt-1 text-gray-900">{{ $course->updated_at->format('d.m.Y H:i') }}</dd>
        </div>
    </dl>
</div>
@endsection

