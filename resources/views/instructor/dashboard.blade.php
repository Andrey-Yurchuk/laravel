@extends('instructor.layouts.instructor')

@section('title', 'Дашборд')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold">Дашборд инструктора</h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Всего курсов</h3>
        <p class="text-3xl font-bold text-indigo-600">{{ $stats['total_courses'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Опубликовано</h3>
        <p class="text-3xl font-bold text-green-600">{{ $stats['published_courses'] }}</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Мои курсы</h2>
        <a href="{{ route('instructor.courses.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
            Создать курс
        </a>
    </div>
    <div class="space-y-4">
        @forelse($courses as $course)
        <div class="border-b pb-4 last:border-b-0">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h3 class="font-semibold text-lg">
                        <a href="{{ route('instructor.courses.show', $course) }}" class="text-indigo-600 hover:text-indigo-800">
                            {{ $course->title }}
                        </a>
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Категория: {{ $course->category->name }} | 
                        Статус: 
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($course->status->value === 'published') bg-green-100 text-green-800
                            @elseif($course->status->value === 'draft') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $course->status->value }}
                        </span>
                    </p>
                </div>
                <div class="ml-4">
                    <a href="{{ route('instructor.courses.edit', $course) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                        Редактировать
                    </a>
                </div>
            </div>
        </div>
        @empty
        <p class="text-gray-500">У вас пока нет курсов. <a href="{{ route('instructor.courses.create') }}" class="text-indigo-600 hover:text-indigo-800">Создайте первый курс</a></p>
        @endforelse
    </div>
    
    @if($courses->hasPages())
    <div class="mt-6">
        {{ $courses->links() }}
    </div>
    @endif
</div>
@endsection

