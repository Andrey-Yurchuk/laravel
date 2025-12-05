<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Instructor\InstructorController;
use App\Http\Controllers\Instructor\InstructorCourseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/register', [
    \App\Http\Controllers\Auth\RegisterController::class,
    'showRegistrationForm'
])->name('register');

Route::post('/register', [
    \App\Http\Controllers\Auth\RegisterController::class,
    'register'
]);

Route::get('/faq', function () {
    return view('pages.faq');
})->name('faq');

Route::get('/dashboard', function () {
    return view('pages.dashboard');
})->name('dashboard');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin routes
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    Route::resource('categories', CategoryController::class);
    Route::resource('courses', CourseController::class);
});

// Instructor routes
Route::prefix('instructor')->name('instructor.')->middleware('auth')->group(function () {
    Route::get('/', [InstructorController::class, 'index'])->name('dashboard');
    Route::resource('courses', InstructorCourseController::class);
});
