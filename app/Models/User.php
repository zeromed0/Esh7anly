<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'avatar',
        'balance', 'daily_limit','is_verified', 'is_banned',
    ];

    protected $hidden = ['password', 'remember_token'];

    // 🧩 العلاقات
    public function orders() {
        return $this->hasMany(Order::class);
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }

    public function wallet() {
        return $this->hasOne(Wallet::class);
    }

    public function verifications() {
        return $this->hasMany(Verification::class);
    }

    public function notifications() {
        return $this->hasMany(Notification::class);
    }

    public function activityLogs() {
        return $this->hasMany(ActivityLog::class);
    }
}