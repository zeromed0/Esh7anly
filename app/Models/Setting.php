<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'tax_percent',
        'tax_enabled',
        'premium_exempt_tax',
        'premium_discount_percent',
        'site_enabled',
        'maintenance_message',
        'min_order_amount',
        'max_order_amount',
        'daily_spend_limit',
        'auto_ban_on_limit',
        'register_bonus',
        'topup_enabled',
        'voucher_enabled',
    ];

    public $timestamps = false; // إذا لم تستخدم created_at/updated_at

    // ==========================
    // جلب قيمة الإعداد
    // ==========================
    public static function get(string $key, $default = null)
    {
        $setting = self::first();
        if (!$setting) return $default;

        return $setting->{$key} ?? $default;
    }

    // ==========================
    // تحديث قيمة الإعداد
    // ==========================
    public static function set(string $key, $value)
    {
        $setting = self::first();

        // إذا لم يوجد سجل، ننشئ واحد جديد
        if (!$setting) {
            $setting = self::create([$key => $value]);
        } else {
            $setting->{$key} = $value;
            $setting->save();
        }

        return $setting->{$key};
    }
}