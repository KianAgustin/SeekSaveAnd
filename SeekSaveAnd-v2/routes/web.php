<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureProfileCompleted;
use Illuminate\Support\Facades\Route;

// ---- Not signed in ----
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/auth/microsoft', [AuthController::class, 'redirectToMicrosoft'])->name('microsoft.redirect');
    Route::get('/auth/microsoft/callback', [AuthController::class, 'handleMicrosoftCallback'])->name('microsoft.callback');

    if (config('seeksave.dev_login')) {
        Route::get('/dev-login/{user}', [AuthController::class, 'devLogin'])->name('dev.login');
    }
});

// ---- Signed in (profile not required yet) ----
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// ---- Signed in + profile completed ----
Route::middleware(['auth', EnsureProfileCompleted::class])->group(function () {
    Route::get('/', [ItemController::class, 'index'])->name('items.index');
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::post('/items/{item}/update', [ItemController::class, 'update'])->name('items.update');
    Route::post('/items/{item}/delete', [ItemController::class, 'destroy'])->name('items.destroy');
    Route::post('/items/{item}/resolve', [ItemController::class, 'resolve'])->name('items.resolve');
    Route::post('/items/{item}/reopen', [ItemController::class, 'reopen'])->name('items.reopen');
    Route::post('/items/{item}/save', [ItemController::class, 'toggleSave'])->name('items.save');

    Route::post('/items/{item}/claim', [ClaimController::class, 'store'])->name('claims.store');
    Route::post('/claims/{claim}/approve', [ClaimController::class, 'approve'])->name('claims.approve');
    Route::post('/claims/{claim}/reject', [ClaimController::class, 'reject'])->name('claims.reject');

    Route::get('/messages', [ConversationController::class, 'index'])->name('messages.index');
    Route::get('/items/{item}/chat', [ConversationController::class, 'open'])->name('messages.open');
    Route::get('/messages/{conversation}', [ConversationController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversation}', [ConversationController::class, 'send'])->name('messages.send');
    Route::get('/messages/{conversation}/poll', [ConversationController::class, 'poll'])->name('messages.poll');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ---- Admin only ----
    Route::middleware(EnsureAdmin::class)->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users/{user}/role', [AdminController::class, 'toggleRole'])->name('users.role');
    });
});
