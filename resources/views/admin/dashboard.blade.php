@extends('admin.layouts.admin')

@section('title', 'Дашборд')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold">Дашборд</h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Категории</h3>
        <p class="text-3xl font-bold text-blue-600">{{ $stats['categories_count'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Всего курсов</h3>
        <p class="text-3xl font-bold text-green-600">{{ $stats['courses_count'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Опубликовано</h3>
        <p class="text-3xl font-bold text-purple-600">{{ $stats['published_courses'] }}</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-2xl font-bold mb-4">Последние курсы</h2>
    <div class="space-y-4">
        @forelse($recentCourses as $course)
        <div class="border-b pb-4 last:border-b-0">
            <h3 class="font-semibold text-lg">{{ $course->title }}</h3>
            <p class="text-sm text-gray-600 mt-1">
                Категория: {{ $course->category->name }} | 
                Преподаватель: {{ $course->instructor->name }} | 
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
        @empty
        <p class="text-gray-500">Курсы не найдены</p>
        @endforelse
    </div>
</div>
@endsection

