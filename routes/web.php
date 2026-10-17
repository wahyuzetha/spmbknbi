<?php

use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

// Public Landing Page
Route::get('/', [LandingController::class, 'index'])->name('home');

// Authentication routes (placeholder/scaffolding)
Route::get('/login', function () {
    return redirect()->route('home', ['#login']);
})->name('login');

Route::get('/daftar', function () {
    return redirect()->route('home', ['#daftar']);
})->name('register');
