<?php

namespace App\Models;

use App\Services\WishlistReservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'age',
        'bio',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'age' => 'integer',
        ];
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')->withTimestamps();
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')->withTimestamps();
    }

    public function ageLabel(): ?string
    {
        if ($this->age === null) {
            return null;
        }

        $n = $this->age % 100;
        $word = match (true) {
            $n % 10 === 1 && $n !== 11 => 'год',
            in_array($n % 10, [2, 3, 4], true) && ! in_array($n, [12, 13, 14], true) => 'года',
            default => 'лет',
        };

        return $this->age.' '.$word;
    }

    public function sendPasswordResetNotification($token): void
    {
        if (app()->environment('local')) {
            session()->flash('reset_url', url(route('password.reset', [
                'token' => $token,
                'email' => $this->email,
            ], false)));
        }

        $this->notify(new \Illuminate\Auth\Notifications\ResetPassword($token));
    }

    protected static function booted(): void
    {
        static::deleting(function (User $user): void {
            $reservations = app(WishlistReservation::class);
            $user->reservations()->get()->each(
                fn (Reservation $reservation) => $reservations->cancel($reservation),
            );
        });
    }
}
