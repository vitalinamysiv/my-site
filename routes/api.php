<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookingController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Наш маршрут для бронирования
Route::post('/wishes/{id}/book', [BookingController::class, 'book'])->middleware('auth:sanctum');