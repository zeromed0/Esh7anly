<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;


class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
public function run()
{
    Setting::create([
        'tax_percent' => 0,
        'tax_enabled' => true,
        'premium_exempt_tax' => true,
        'premium_discount_percent' => 0,
        'site_enabled' => true,
        'maintenance_message' => null,
        'min_order_amount' => 1,
        'max_order_amount' => 100000,
        'daily_spend_limit' => 30000,
        'auto_ban_on_limit' => true,
        'register_bonus' => 0,
        'topup_enabled' => true,
        'voucher_enabled' => true,
    ]);
}
}
