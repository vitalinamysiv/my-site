<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    // Вот эта строка разрешает запись:
    protected $fillable = ['user_id', 'title', 'description', 'is_public'];
    
    public function user() { return $this->belongsTo(User::class); }
    public function wishes() { return $this->hasMany(Wish::class); }
}