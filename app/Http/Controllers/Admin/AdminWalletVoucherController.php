<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\WalletVoucher;
use Illuminate\Support\Str;

class AdminWalletVoucherController extends Controller
{
    // عرض صفحة الأكواد
    public function index()
    {
        $vouchers = WalletVoucher::all();
        return Inertia::render('Admin/WalletVouchers', ['vouchers' => $vouchers]);
    }

    // إنشاء كود تلقائي بقيمة معينة
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        // توليد كود فريد من 16 حرف وأرقام
        do {
            $code = Str::upper(Str::random(16)); // أحرف كبيرة فقط، إذا تريد مزيج من الأحرف الكبيرة والصغيرة والأرقام يمكن تعديلها
        } while (WalletVoucher::where('code', $code)->exists());

        WalletVoucher::create([
            'code' => $code,
            'amount' => $request->amount,
            'is_used' => false,
            'used_by' => null,
        ]);

        return back()->with('success', 'تم إنشاء الكود بنجاح: '.$code);
    }

    // حذف كود
    public function destroy($id)
    {
        $voucher = WalletVoucher::findOrFail($id);
        $voucher->delete();
        return back();
    }
}