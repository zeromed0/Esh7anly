<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'game_id', 'offer_id', 'type', 'player_id',
        'quantity', 'total_price', 'status'
    ];

    
public function user() {
    return $this->belongsTo(User::class);
}

public function game() {
    return $this->belongsTo(Game::class);
}

public function offer() {
    return $this->belongsTo(Offer::class);
}

    public function voucherItems() {
        return $this->hasMany(VoucherOrderItem::class);
    }

    
public function voucherCodes()
{
    return $this->hasMany(VoucherCode::class);
}
}