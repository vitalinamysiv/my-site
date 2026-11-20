<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function delete(User $user, Reservation $reservation): bool
    {
        return (int) $reservation->user_id === (int) $user->id;
    }
}
