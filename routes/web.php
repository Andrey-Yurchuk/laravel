<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/register', function () {
    return view('pages.register');
})->name('register');

Route::get('/faq', function () {
    return view('pages.faq');
})->name('faq');

Route::get('/dashboard', function () {
    return view('pages.dashboard');
})->name('dashboard');

Route::get('/login', function () {
    return redirect()->route('register');
})->name('login');
