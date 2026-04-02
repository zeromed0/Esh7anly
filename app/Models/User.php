<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'national_id',
        'balance',
        'is_premium',
        'premium_until',
        'is_banned',
        'id_card_image',
        'selfie_with_id_image',
        'verification_status'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'premium_until' => 'datetime',
        'email_verified_at' => 'datetime', // ✅ مهم
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

    // ⭐ حالة البريميوم
    public function getIsPremiumActiveAttribute()
    {
        return $this->premium_until && $this->premium_until > now();
    }
}