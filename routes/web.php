<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EbookController;

/* |-------------------------------------------------------------------------- | Web Routes |-------------------------------------------------------------------------- | | Here is where you can register web routes for your application. These | routes are loaded by the RouteServiceProvider and all of them will | be assigned to the "web" middleware group. Make something great! | */

use App\Http\Controllers\AuthController;

// Public Routes
Route::get('/', [EbookController::class , 'index'])->name('ebooks.index');
Route::get('/read/{ebook}', [EbookController::class , 'read'])->name('ebooks.read');
Route::get('/stream/{ebook}', [EbookController::class , 'stream'])->name('ebooks.stream');

// Auth Routes
Route::get('/login', [AuthController::class , 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class , 'login']);
Route::post('/logout', [AuthController::class , 'logout'])->name('logout');

// Protected Admin Routes
Route::middleware('auth')->group(function () {
    Route::get('/upload', [EbookController::class , 'create'])->name('ebooks.create');
    Route::post('/upload', [EbookController::class , 'store'])->name('ebooks.store');

    // Admin Panel
    Route::get('/admin', [\App\Http\Controllers\AdminController::class , 'index'])->name('admin.index');
    Route::put('/admin/ebooks/{ebook}/genre', [\App\Http\Controllers\AdminController::class , 'updateGenre'])->name('admin.update-genre');
    Route::delete('/admin/ebooks/{ebook}', [\App\Http\Controllers\AdminController::class , 'destroy'])->name('admin.destroy');
});
