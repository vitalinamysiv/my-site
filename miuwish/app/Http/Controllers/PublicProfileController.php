<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use App\Support\WishlistFilters;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicProfileController extends Controller
{
    public function show(Request $request, User $user): View
    {
        $wishlists = WishlistFilters::apply(
            $user->wishlists()->with('category'),
            $request,
        )->get();

        $followed = $request->user()
            ? $request->user()->following()->whereKey($user->id)->exists()
            : false;

        return view('profiles.show', [
            'profile' => $user,
            'wishlists' => $wishlists,
            'categories' => Category::query()->orderBy('name')->get(),
            'followed' => $followed,
        ]);
    }
}
