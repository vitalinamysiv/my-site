<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wishlist;

class WishlistPolicy
{
    public function update(User $user, Wishlist $wishlist): bool
    {
        return (int) $wishlist->user_id === (int) $user->id;
    }

    public function delete(User $user, Wishlist $wishlist): bool
    {
        return (int) $wishlist->user_id === (int) $user->id;
    }
}
