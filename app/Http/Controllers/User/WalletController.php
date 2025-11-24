<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\WalletVoucher;

class WalletController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $availableVouchers = WalletVoucher::whereNull('used_by')->get();

        $usedVouchers = WalletVoucher::where('used_by', $user->id)
            ->orderByDesc('used_at')
            ->get();

        return Inertia::render('Wallet', [
            'user' => $user,
            'availableVouchers' => $availableVouchers,
            'usedVouchers' => $usedVouchers,
        ]);
    }

    public function redeemVoucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:wallet_vouchers,code',
        ]);

        $user = auth()->user();
        $voucher = WalletVoucher::where('code', $request->code)->first();

        if (!$voucher) {
            return back()->with('error', 'This code does not exist.');
        }

        // تحقق من أن الكود لم يُستخدم مسبقًا
        if ($voucher->is_used) {
        return back()->with('error', 'الكود مستخدم بالفعل.');
    }

        // تحديث حالة الكود
        $voucher->update([
            'is_used' => 1,
            'used_by' => $user->id,
            'used_at' => now(),
        ]);

        // إضافة الرصيد
        $user->increment('balance', $voucher->amount);

        return redirect()->route('user.wallet.index')->with('success', 'Voucher redeemed successfully!');
    }
}