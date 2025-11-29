<?php

namespace App\DTOs;

class CourseDTO
{
    public function __construct(
        public readonly int $instructorId,
        public readonly int $categoryId,
        public readonly string $title,
        public readonly string $slug,
        public readonly string $description,
        public readonly string $difficultyLevel,
        public readonly string $status,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            instructorId: (int) $data['instructor_id'],
            categoryId: (int) $data['category_id'],
            title: $data['title'],
            slug: $data['slug'] ?? '',
            description: $data['description'],
            difficultyLevel: $data['difficulty_level'],
            status: $data['status'],
        );
    }

    public function toArray(): array
    {
        return [
            'instructor_id' => $this->instructorId,
            'category_id' => $this->categoryId,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'difficulty_level' => $this->difficultyLevel,
            'status' => $this->status,
        ];
    }
}
