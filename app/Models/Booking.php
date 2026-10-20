<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    //
public function wish() { return $this->belongsTo(Wish::class); }
public function user() { return $this->belongsTo(User::class); }}
