<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\CategoryServiceInterface;
use App\DTOs\CategoryDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCategoryRequest;
use App\Http\Requests\Api\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryServiceInterface $categoryService
    ) {
    }

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Category::class);
        $categories = $this->categoryService->getAll();

        return response()->json($categories);
    }

    public function show(Category $category): JsonResponse
    {
        $category = $this->categoryService->getById($category->id);
        $this->authorize('view', $category);

        return response()->json($category);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $dto = CategoryDTO::fromArray($request->validated());
        $category = $this->categoryService->create($dto);

        return response()->json($category, 201);
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category = $this->categoryService->getById($category->id);

        $data = array_merge(
            [
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
            ],
            $request->validated()
        );
        $dto = CategoryDTO::fromArray($data);
        $category = $this->categoryService->update($category->id, $dto);

        return response()->json($category);
    }

    public function destroy(Category $category): JsonResponse
    {
        $category = $this->categoryService->getById($category->id);
        $this->authorize('delete', $category);
        $this->categoryService->delete($category->id);

        return response()->json(null, 204);
    }
}
