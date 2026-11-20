<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Wishlist;
use App\Services\WishlistReservation as ReservationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function index(Request $request): View
    {
        $reservations = $request->user()
            ->reservations()
            ->with(['wishlist.category', 'wishlist.user'])
            ->latest()
            ->get();

        return view('reservations.index', [
            'reservations' => $reservations,
        ]);
    }

    public function create(Request $request, Wishlist $wishlist): View|RedirectResponse
    {
        $this->guard($request, $wishlist);

        if ($wishlist->isReserved() || $wishlist->reservation()->exists()) {
            return redirect()->route('wishes.show', $wishlist);
        }

        $wishlist->load('category', 'user');

        return view('reservations.create', [
            'wishlist' => $wishlist,
        ]);
    }

    public function store(Request $request, Wishlist $wishlist, ReservationService $reservations): RedirectResponse
    {
        $this->guard($request, $wishlist);
        $reservations->reserve($wishlist, $request->user());

        return redirect()
            ->route('reservations.index')
            ->with('status', 'Желание забронировано. Владелец не увидит ваше имя.');
    }

    public function destroy(Request $request, Reservation $reservation, ReservationService $reservations): RedirectResponse
    {
        $this->authorize('delete', $reservation);
        $reservations->cancel($reservation);

        return redirect()
            ->route('reservations.index')
            ->with('status', 'Бронь снята. Желание снова доступно.');
    }

    private function guard(Request $request, Wishlist $wishlist): void
    {
        if ((int) $wishlist->user_id !== (int) $request->user()->id) {
            return;
        }

        throw ValidationException::withMessages([
            'wishlist' => 'Своё желание бронировать не нужно.',
        ])->redirectTo(route('wishes.show', $wishlist));
    }
}
