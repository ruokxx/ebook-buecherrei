<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EbookController;

/* |-------------------------------------------------------------------------- | Web Routes |-------------------------------------------------------------------------- | | Here is where you can register web routes for your application. These | routes are loaded by the RouteServiceProvider and all of them will | be assigned to the "web" middleware group. Make something great! | */

use App\Http\Controllers\AuthController;

// Public Routes
Route::get('/', [EbookController::class , 'index'])->name('ebooks.index');
Route::get('/read/{ebook}', [EbookController::class , 'read'])->name('ebooks.read');
Route::get('/stream/{ebook}', [EbookController::class , 'stream'])->name('ebooks.stream');

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// Auth Routes
Route::get('/login', [AuthController::class , 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class , 'login']);
Route::get('/register', [AuthController::class , 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class , 'register']);
Route::post('/logout', [AuthController::class , 'logout'])->name('logout');

// Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Bestätigungslink wurde erneut gesendet!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// User Protected Routes
Route::middleware('auth')->group(function () {
    Route::post('/ebooks/{ebook}/progress', [EbookController::class , 'saveProgress'])->name('ebooks.progress')->middleware('verified');
});

// Admin Protected Routes
Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])->group(function () {
    Route::get('/upload', [EbookController::class , 'create'])->name('ebooks.create');
    Route::post('/upload', [EbookController::class , 'store'])->name('ebooks.store');

    // Admin Panel
    Route::get('/admin', [\App\Http\Controllers\AdminController::class , 'index'])->name('admin.index');
    Route::put('/admin/ebooks/{ebook}/genre', [\App\Http\Controllers\AdminController::class , 'updateGenre'])->name('admin.update-genre');
    Route::delete('/admin/ebooks/{ebook}', [\App\Http\Controllers\AdminController::class , 'destroy'])->name('admin.destroy');

    // Admin Users
    Route::get('/admin/users', [\App\Http\Controllers\AdminController::class , 'users'])->name('admin.users');
    Route::put('/admin/users/{user}/toggle-admin', [\App\Http\Controllers\AdminController::class , 'toggleAdmin'])->name('admin.users.toggle-admin');
    Route::delete('/admin/users/{user}', [\App\Http\Controllers\AdminController::class , 'deleteUser'])->name('admin.users.destroy');

    // Admin Settings
    Route::get('/admin/settings', [\App\Http\Controllers\AdminController::class , 'settings'])->name('admin.settings');
    Route::put('/admin/settings', [\App\Http\Controllers\AdminController::class , 'updateSettings'])->name('admin.settings.update');
});
