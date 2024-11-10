<?php

use App\Http\Controllers\FinalProjectController;
use App\Http\Controllers\FinalProjectSeminarController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InternshipController;
use App\Http\Controllers\InternshipSeminarController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';
// Auth::routes();

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('can:dashboard');

    Route::get('/internship/list', [InternshipController::class, 'index'])->name('internship.list');
    Route::get('/internship/detail/{id}', [InternshipController::class, 'detail'])->name('internship.detail');
    Route::get('/internship/create', [InternshipController::class, 'create'])->name('internship.create');
    Route::post('/internship/save', [InternshipController::class, 'save'])->name('internship.save');
    Route::get('/internship/edit/{id}', [InternshipController::class, 'edit'])->name('internship.edit');
    Route::patch('/internship/update/{id}', [InternshipController::class, 'update'])->name('internship.update');
    Route::patch('/internship/approve/{id}', [InternshipController::class, 'approve'])->name('internship.approve');
    Route::patch('/internship/deny/{id}', [InternshipController::class, 'deny'])->name('internship.deny');

    Route::get('/final-project/list', [FinalProjectController::class, 'index'])->name('finalproject.list');
    Route::get('/final-project/detail/{id}', [FinalProjectController::class, 'detail'])->name('finalproject.detail');
    Route::get('/final-project/create', [FinalProjectController::class, 'create'])->name('finalproject.create');
    Route::post('/final-project/save', [FinalProjectController::class, 'save'])->name('finalproject.save');
    Route::get('/final-project/edit/{id}', [FinalProjectController::class, 'edit'])->name('finalproject.edit');
    Route::patch('/final-project/update/{id}', [FinalProjectController::class, 'update'])->name('finalproject.update');
    Route::patch('/final-project/approve/{id}', [FinalProjectController::class, 'approve'])->name('finalproject.approve');
    Route::patch('/final-project/deny/{id}', [FinalProjectController::class, 'deny'])->name('finalproject.deny');

    Route::get('/internship-seminar/list', [InternshipSeminarController::class, 'index'])->name('internship-seminar.list');
    Route::get('/internship-seminar/detail/{id}', [InternshipSeminarController::class, 'detail'])->name('internship-seminar.detail');
    Route::get('/internship-seminar/create', [InternshipSeminarController::class, 'create'])->name('internship-seminar.create');
    Route::post('/internship-seminar/save', [InternshipSeminarController::class, 'save'])->name('internship-seminar.save');
    Route::get('/internship-seminar/edit/{id}', [InternshipSeminarController::class, 'edit'])->name('internship-seminar.edit');
    Route::patch('/internship-seminar/update/{id}', [InternshipSeminarController::class, 'update'])->name('internship-seminar.update');
    Route::patch('/internship-seminar/approve/{id}', [InternshipSeminarController::class, 'approve'])->name('internship-seminar.approve');
    Route::patch('/internship-seminar/deny/{id}', [InternshipSeminarController::class, 'deny'])->name('internship-seminar.deny');

    Route::get('/final-project-seminar/list', [FinalProjectSeminarController::class, 'index'])->name('finalproject-seminar.list');
    Route::get('/final-project-seminar/detail/{id}', [FinalProjectSeminarController::class, 'detail'])->name('finalproject-seminar.detail');
    Route::get('/final-project-seminar/create', [FinalProjectSeminarController::class, 'create'])->name('finalproject-seminar.create');
    Route::post('/final-project-seminar/save', [FinalProjectSeminarController::class, 'save'])->name('finalproject-seminar.save');
    Route::get('/final-project-seminar/edit/{id}', [FinalProjectSeminarController::class, 'edit'])->name('finalproject-seminar.edit');
    Route::patch('/final-project-seminar/update/{id}', [FinalProjectSeminarController::class, 'update'])->name('finalproject-seminar.update');
    Route::patch('/final-project-seminar/approve/{id}', [FinalProjectSeminarController::class, 'approve'])->name('finalproject-seminar.approve');
    Route::patch('/final-project-seminar/deny/{id}', [FinalProjectSeminarController::class, 'deny'])->name('finalproject-seminar.deny');
});

