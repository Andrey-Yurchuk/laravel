@extends('admin.layouts.admin')

@section('title', 'Категории')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold">Категории</h1>
    <a href="{{ route('admin.categories.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        Создать категорию
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Название</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">URL</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Действия</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($categories as $category)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">{{ $category->id }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $category->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $category->slug }}</td>
                <td class="px-6 py-4 whitespace-nowrap space-x-2">
                    <a href="{{ route('admin.categories.show', $category) }}" class="text-blue-600 hover:text-blue-800">Просмотр</a>
                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-green-600 hover:text-green-800">Редактировать</a>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Удалить категорию?')">Удалить</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Категории не найдены</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

