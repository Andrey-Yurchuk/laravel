<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\CategoryServiceInterface;
use App\DTOs\CategoryDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryServiceInterface $categoryService
    ) {}

    public function index(): View
    {
        $categories = $this->categoryService->getAll();
        
        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $dto = CategoryDTO::fromArray($request->validated());
        
        try {
            $this->categoryService->create($dto);
            return redirect()->route('admin.categories.index')
                ->with('success', 'Категория успешно создана');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(int $id): View
    {
        $category = $this->categoryService->getById($id);
        
        return view('admin.categories.show', compact('category'));
    }

    public function edit(int $id): View
    {
        $category = $this->categoryService->getById($id);
        
        return view('admin.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, int $id): RedirectResponse
    {
        $dto = CategoryDTO::fromArray($request->validated());
        
        try {
            $this->categoryService->update($id, $dto);
            return redirect()->route('admin.categories.index')
                ->with('success', 'Категория успешно обновлена');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->categoryService->delete($id);
            return redirect()->route('admin.categories.index')
                ->with('success', 'Категория успешно удалена');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
