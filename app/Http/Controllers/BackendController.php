<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wish;
use App\Models\Booking;

class BackendController extends Controller
{
    public function index()
    {
        // Считаем статистику из базы данных
        $stats = [
            'users' => User::count(),
            'wishes' => Wish::count(),
            'bookings' => Booking::count()
        ];

        // Берем последние 5 желаний
        $recentWishes = Wish::latest()->take(5)->get();

        // Передаем данные в шаблон
        return view('backend', compact('stats', 'recentWishes'));
    }
}