<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'balance','is_premium','premium_until', 'is_banned',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
    'premium_until' => 'datetime',
];

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



public function getIsPremiumActiveAttribute()
{
    return $this->premium_until && $this->premium_until->isFuture();
}
}