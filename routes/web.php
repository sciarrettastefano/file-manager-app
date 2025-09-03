<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


Route::controller(\App\Http\Controllers\UserController::class)
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/users', 'index')->name('users.index');
        Route::post('/users/create', 'create')->name('users.create');
        Route::patch('/users/{user}/status', 'changeStatus')->name('users.changeStatus');
        Route::put('/users/{user}/edit', 'edit')->name('users.edit');
        Route::patch('/users/mass-change-status', 'massChangeStatus')->name('users.massChangeStatus');
    });


Route::controller(\App\Http\Controllers\FileController::class)
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/my-files/{folder?}', 'index')
            ->where('folder', '(.*)')
            ->name('files.index');
        Route::post('/files/create', 'createFolder')->name('files.createFolder');
        Route::post('/files/store', 'store')->name('files.store');
        Route::get('/files/download', 'download')->name('files.download');
    });


//si può cancellare più avanti
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';
