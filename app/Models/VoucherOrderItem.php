<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VoucherOrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'voucher_code_id'];

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function voucherCode() {
        return $this->belongsTo(VoucherCode::class);
    }
}