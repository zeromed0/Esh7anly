<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WalletVoucher extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'amount', 'is_used', 'used_at', 'used_by'];
}