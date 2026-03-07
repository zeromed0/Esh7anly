<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Setting;

class AdminSettingsController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Settings', [
            'settings' => [
                'tax_percent' => Setting::get('tax_percent', 10),
                'tax_enabled' => Setting::get('tax_enabled', true),
                'premium_exempt_tax' => Setting::get('premium_exempt_tax', true),
                'premium_discount_percent' => Setting::get('premium_discount_percent', 0),
                'daily_spend_limit' => Setting::get('daily_spend_limit', 30000),
                'auto_ban_on_limit' => Setting::get('auto_ban_on_limit', true),
                'site_enabled' => Setting::get('site_enabled', true),
                'maintenance_message' => Setting::get('maintenance_message', 'الموقع تحت الصيانة حالياً'),
                'min_order_amount' => Setting::get('min_order_amount', 0),
                'max_order_amount' => Setting::get('max_order_amount', 100000),
                'register_bonus' => Setting::get('register_bonus', 0),
                'topup_enabled' => Setting::get('topup_enabled', true),
                'voucher_enabled' => Setting::get('voucher_enabled', true),
            ]
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'tax_percent' => 'required|numeric|min:0',
            'tax_enabled' => 'required|boolean',
            'premium_exempt_tax' => 'required|boolean',
            'premium_discount_percent' => 'required|numeric|min:0',
            'daily_spend_limit' => 'required|numeric|min:0',
            'auto_ban_on_limit' => 'required|boolean',
            'site_enabled' => 'required|boolean',
            'maintenance_message' => 'nullable|string',
            'min_order_amount' => 'required|numeric|min:0',
            'max_order_amount' => 'required|numeric|min:0',
            'register_bonus' => 'required|numeric|min:0',
            'topup_enabled' => 'required|boolean',
            'voucher_enabled' => 'required|boolean',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', '✅ تم تحديث الإعدادات بنجاح');
    }
}