<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
// Пункт 3: Маршрут /hello
Route::get('/hello', function () {
    return 'Hello Laravel';
});

// Пункт 3: Маршрут /hello/{name}
Route::get('/hello/{name}', function ($name) {
    return 'Hello ' . $name;
});

// Пункт 4: Страница /about
Route::get('/about', function () {
    return 'Имя: Виталина Мысив, Группа: БПИ-24-1';
});

use App\Http\Controllers\BackendController;

Route::get('/backend', [BackendController::class, 'index']);