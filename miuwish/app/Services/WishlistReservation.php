<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WishlistReservation
{
    public function reserve(Wishlist $wishlist, User $user): void
    {
        if ((int) $wishlist->user_id === (int) $user->id) {
            abort(403);
        }

        DB::transaction(function () use ($wishlist, $user) {
            $locked = Wishlist::query()->whereKey($wishlist->id)->lockForUpdate()->firstOrFail();
            $existing = $locked->reservation()->first();

            if ($existing !== null && (int) $existing->user_id === (int) $user->id) {
                if ($locked->status !== Wishlist::RESERVED) {
                    $locked->update(['status' => Wishlist::RESERVED]);
                }

                return;
            }

            if ($existing !== null || $locked->status !== Wishlist::AVAILABLE) {
                throw ValidationException::withMessages([
                    'wishlist' => 'Это желание уже забронировано.',
                ]);
            }

            $locked->reservation()->create([
                'user_id' => $user->id,
            ]);
            $locked->update([
                'status' => Wishlist::RESERVED,
            ]);
        });
    }

    public function cancel(Reservation $reservation): void
    {
        DB::transaction(function () use ($reservation) {
            $wishlist = Wishlist::query()->whereKey($reservation->wishlist_id)->lockForUpdate()->first();
            $reservation->delete();
            if ($wishlist !== null) {
                $wishlist->update([
                    'status' => Wishlist::AVAILABLE,
                ]);
            }
        });
    }
}
