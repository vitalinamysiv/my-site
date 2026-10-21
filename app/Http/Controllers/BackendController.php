<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BackendController extends Controller
{
    public function index()
    {
        // Эта строка говорит Laravel: "Покажи файл backend.blade.php"
        return view('backend'); 
    }
}