<?php

namespace App\Http\Requests\Api;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return Auth::check() && $user && $user->role === UserRole::Admin;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category');

        if ($categoryId instanceof \App\Models\Category) {
            $categoryId = $categoryId->id;
        }

        return [
            'name' => 'sometimes|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($categoryId),
            ],
            'description' => 'nullable|string',
        ];
    }
}
