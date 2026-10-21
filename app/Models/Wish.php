<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Wish extends Model
{
    // Вот эта строка разрешает запись:
    protected $fillable = ['wishlist_id', 'title', 'image', 'price', 'category', 'description', 'url'];
    
    public function wishlist() { return $this->belongsTo(Wishlist::class); }
    public function booking() { return $this->hasOne(Booking::class); }
}