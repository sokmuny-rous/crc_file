<?php

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('files.index');
    }

    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/files', [FileController::class, 'index'])->name('files.index');
    Route::get('/files/{sharedFile}/thumbnail', [FileController::class, 'thumbnail'])->name('files.thumbnail');
    Route::get('/files/{sharedFile}/preview', [FileController::class, 'preview'])->name('files.preview');
    Route::get('/files/{sharedFile}/download', [FileController::class, 'download'])->name('files.download');

    Route::middleware('role:editor,admin')->group(function (): void {
        Route::get('/files/create', [FileController::class, 'create'])->name('files.create');
        Route::post('/files', [FileController::class, 'store'])->name('files.store');
        Route::get('/files/{sharedFile}/edit', [FileController::class, 'edit'])->name('files.edit');
        Route::put('/files/{sharedFile}', [FileController::class, 'update'])->name('files.update');
        Route::delete('/files/{sharedFile}', [FileController::class, 'destroy'])->name('files.destroy');

        Route::resource('categories', CategoryController::class)->except(['show']);
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.role');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    });
});
