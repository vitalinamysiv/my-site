<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wish extends Model
{
    //
public function wishlist() { return $this->belongsTo(Wishlist::class); }
public function booking() { return $this->hasOne(Booking::class); } }
