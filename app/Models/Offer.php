<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id', 'name', 'type', 'price', 'image', 'is_active'
    ];

    public function game() {
        return $this->belongsTo(Game::class);
    }

    public function voucherCodes() {
        return $this->hasMany(VoucherCode::class);
    }

    public function orders() {
        return $this->hasMany(Order::class);
    }
}