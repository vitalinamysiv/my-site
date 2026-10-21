<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wish;
use App\Models\Booking;

class BookingController extends Controller
{
    public function book(Request $request, $wish_id)
    {
        $wish = Wish::find($wish_id);

        // Если желания нет - ошибка 404
        if (!$wish) {
            return response()->json(['error' => 'Желание не найдено'], 404);
        }

        // ПРАВИЛО 1: Нельзя бронировать свое же желание (ошибка 403)
        // (Предполагаем, что у желания есть связь с владельцем через wishlist->user_id)
        if ($wish->wishlist->user_id === $request->user()->id) {
            return response()->json(['error' => 'Нельзя бронировать свое желание'], 403);
        }

        // ПРАВИЛО 2: Нельзя бронировать уже забронированное желание (ошибка 409)
        if ($wish->booking()->exists()) {
            return response()->json(['error' => 'Желание уже забронировано'], 409); 
        }

        // Если проверки пройдены — создаем бронь
        Booking::create([
            'wish_id' => $wish->id,
            'user_id' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Успешно забронировано'], 201);
    }
}