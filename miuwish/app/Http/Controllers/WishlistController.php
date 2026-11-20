<?php

namespace App\Http\Controllers;

use App\Http\Requests\WishlistRequest;
use App\Models\Category;
use App\Models\Wishlist;
use App\Support\StoredImage;
use App\Support\WishlistFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(Request $request): View
    {
        $wishlists = WishlistFilters::apply(
            $request->user()->wishlists()->with('category'),
            $request,
        )->get();

        return view('wishes.index', [
            'wishlists' => $wishlists,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('wishes.create', [
            'categories' => Category::query()->orderBy('name')->get(),
            'wishlist' => new Wishlist,
        ]);
    }

    public function store(WishlistRequest $request): RedirectResponse
    {
        $wishlist = $request->user()->wishlists()->create([
            ...$request->safe()->except('image'),
            'status' => Wishlist::AVAILABLE,
            'image' => $request->hasFile('image')
                ? StoredImage::store($request->file('image'), 'wishes')
                : null,
        ]);

        return redirect()->route('wishes.show', $wishlist);
    }

    public function show(Request $request, Wishlist $wishlist): View
    {
        $wishlist->load('category', 'user');
        $myReservationId = $request->user()
            ? $wishlist->reservation()->where('user_id', $request->user()->id)->value('id')
            : null;

        return view('wishes.show', [
            'wishlist' => $wishlist,
            'myReservationId' => $myReservationId,
        ]);
    }

    public function edit(Request $request, Wishlist $wishlist): View
    {
        $this->authorize('update', $wishlist);

        return view('wishes.edit', [
            'wishlist' => $wishlist,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function update(WishlistRequest $request, Wishlist $wishlist): RedirectResponse
    {
        $this->authorize('update', $wishlist);

        $attributes = $request->safe()->except('image');
        if ($request->hasFile('image')) {
            $attributes['image'] = StoredImage::store($request->file('image'), 'wishes');
            StoredImage::delete($wishlist->image);
        }

        $wishlist->update($attributes);

        return redirect()->route('wishes.show', $wishlist);
    }

    public function destroy(Request $request, Wishlist $wishlist): RedirectResponse
    {
        $this->authorize('delete', $wishlist);
        StoredImage::delete($wishlist->image);
        $wishlist->delete();

        return redirect()->route('wishes.index')->with('status', 'Желание удалено.');
    }
}
