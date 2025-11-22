<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->words(2, true);

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'description' => fake()->optional()->paragraph(),
        ];
    }

    public function programming(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Programming',
            'slug' => 'programming',
            'description' => 'Courses about coding and software development.',
        ]);
    }

    public function design(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Design',
            'slug' => 'design',
            'description' => 'Courses focused on design and creativity.',
        ]);
    }

    public function marketing(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Marketing',
            'slug' => 'marketing',
            'description' => 'Courses about digital marketing and strategy.',
        ]);
    }
}
