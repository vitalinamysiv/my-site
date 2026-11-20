<?php

use App\Http\Controllers\FriendController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/u/{user:username}', [PublicProfileController::class, 'show'])->name('profiles.show');
Route::get('/wishes/{wishlist}', [WishlistController::class, 'show'])->whereNumber('wishlist')->name('wishes.show');

Route::middleware('auth')->group(function () {
    Route::get('/wishes', [WishlistController::class, 'index'])->name('wishes.index');
    Route::get('/wishes/create', [WishlistController::class, 'create'])->name('wishes.create');
    Route::post('/wishes', [WishlistController::class, 'store'])->name('wishes.store');
    Route::get('/wishes/{wishlist}/edit', [WishlistController::class, 'edit'])->name('wishes.edit');
    Route::put('/wishes/{wishlist}', [WishlistController::class, 'update'])->name('wishes.update');
    Route::delete('/wishes/{wishlist}', [WishlistController::class, 'destroy'])->name('wishes.destroy');

    Route::get('/friends', [FriendController::class, 'index'])->name('friends.index');
    Route::post('/friends/{user:username}', [FriendController::class, 'store'])->name('friends.store');
    Route::delete('/friends/{user:username}', [FriendController::class, 'destroy'])->name('friends.destroy');

    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/wishes/{wishlist}/reserve', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/wishes/{wishlist}/reserve', [ReservationController::class, 'store'])->name('reservations.store');
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::redirect('/dashboard', '/wishes');

require __DIR__.'/auth.php';
