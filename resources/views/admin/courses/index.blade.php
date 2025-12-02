@extends('admin.layouts.admin')

@section('title', 'Курсы')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold">Курсы</h1>
    <a href="{{ route('admin.courses.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        Создать курс
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Название</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Категория</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Преподаватель</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Действия</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($courses as $course)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">{{ $course->id }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $course->title }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $course->category->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $course->instructor->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs rounded-full 
                        @if($course->status->value === 'published') bg-green-100 text-green-800
                        @elseif($course->status->value === 'draft') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ $course->status->value }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap space-x-2">
                    <a href="{{ route('admin.courses.show', $course) }}" class="text-blue-600 hover:text-blue-800">Просмотр</a>
                    <a href="{{ route('admin.courses.edit', $course) }}" class="text-green-600 hover:text-green-800">Редактировать</a>
                    <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Удалить курс?')">Удалить</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Курсы не найдены</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($courses->hasPages())
    <div class="mt-4">
        {{ $courses->links() }}
    </div>
@endif
@endsection

