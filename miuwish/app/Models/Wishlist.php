<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Wishlist extends Model
{
    public const AVAILABLE = 'available';

    public const RESERVED = 'reserved';

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'price',
        'url',
        'image',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reservation(): HasOne
    {
        return $this->hasOne(Reservation::class);
    }

    public function isReserved(): bool
    {
        return $this->status === self::RESERVED;
    }

    public function statusLabel(): string
    {
        return $this->isReserved() ? 'Забронировано' : 'Доступно';
    }

    public function priceLabel(): string
    {
        $formatted = number_format((float) $this->price, 2, ',', ' ');

        return rtrim(rtrim($formatted, '0'), ',').' ₽';
    }
}
