<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\InternshipController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';
// Auth::routes();

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard')->middleware('can:dashboard');

    Route::get('/internship/list', [InternshipController::class, 'index'])->name('internship.list');
    Route::get('/internship/detail/{id}', [InternshipController::class, 'detail'])->name('internship.detail');
    Route::get('/internship/create', [InternshipController::class, 'create'])->name('internship.create');
    Route::post('/internship/save', [InternshipController::class, 'save'])->name('internship.save');
    Route::get('/internship/edit/{id}', [InternshipController::class, 'edit'])->name('internship.edit');
    Route::patch('/internship/update/{id}', [InternshipController::class, 'update'])->name('internship.update');
    Route::patch('/internship/approve/{id}', [InternshipController::class, 'approve'])->name('internship.approve');
    Route::patch('/internship/deny/{id}', [InternshipController::class, 'deny'])->name('internship.deny');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
