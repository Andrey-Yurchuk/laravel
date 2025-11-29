<?php

namespace App\DTOs;

class CategoryDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $description = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            slug: $data['slug'] ?? '',
            description: $data['description'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
        ], fn($value) => $value !== null);
    }
}

