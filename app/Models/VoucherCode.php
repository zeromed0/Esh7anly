<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VoucherCode extends Model
{
    use HasFactory;

    protected $fillable = ['offer_id','order_id', 'code', 'is_used'];

    public function offer() {
        return $this->belongsTo(Offer::class);
    }

    public function orderItems() {
        return $this->hasMany(VoucherOrderItem::class);
    }
    public function order() {
        return $this->hasMany(Order::class);
    }
}