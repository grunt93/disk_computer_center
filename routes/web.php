<?php

use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\{Auth, Route};

Route::get('/', fn() => view('welcome'));

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// 個人資料路由群組
Route::controller(ProfileController::class)
    ->middleware('auth')
    ->prefix('profile')
    ->name('profile.')
    ->group(function () {
        Route::get('/', 'show')->name('show');
        Route::get('/edit', 'edit')->name('edit');
        Route::put('/', 'update')->name('update');
        Route::put('/password', 'updatePassword')->name('password');
        Route::delete('/', 'delete')->name('delete');
    });

