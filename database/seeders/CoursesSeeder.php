<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CoursesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $instructorId = DB::table('users')->insertGetId([
                'name' => 'Ivan Instructor',
                'email' => 'instructor@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $categoryId = DB::table('categories')->where('slug', 'programming')->value('id');

            $courseId = DB::table('courses')->insertGetId([
                'instructor_id' => $instructorId,
                'category_id' => $categoryId ?? DB::table('categories')->insertGetId([
                    'name' => 'Programming',
                    'slug' => 'programming',
                    'description' => 'Courses about coding and software development.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]),
                'title' => 'Laravel From Scratch',
                'slug' => 'laravel-from-scratch',
                'description' => 'Learn Laravel by building a real SaaS platform step-by-step.',
                'difficulty_level' => 'beginner',
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('course_plans')->insert([
                'course_id' => $courseId,
                'name' => 'Basic Access',
                'description' => 'Access to all lessons.',
                'price_monthly' => 19.99,
                'price_yearly' => 199.99,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    }
}
