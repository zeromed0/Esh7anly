<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Game;
use App\Models\Offer;
use App\Models\Order;
use App\Models\VoucherCode;
use App\Models\Transaction;

class VoucherController extends Controller
{
    public function index()
    {
        $games = Game::where('type', 'voucher')->with('offers')->get();

        return Inertia::render('Vouchers', [
            'games' => $games,
            'user' => auth()->user(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
            'offer_id' => 'required|exists:offers,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = auth()->user();
        $offer = Offer::findOrFail($request->offer_id);

        // تحقق من توفر الأكواد
        $codes = VoucherCode::where('offer_id', $offer->id)
            ->where('is_used', false)
            ->take($request->quantity)
            ->get();

        if ($codes->count() < $request->quantity) {
            return back()->withErrors(['error' => '❌ لا توجد أكواد كافية لهذا العرض.']);
        }

        $total = $offer->price * $request->quantity;

        // فحص الرصيد
        if ($user->balance < $total) {
            return back()->withErrors(['error' => 'رصيدك غير كافٍ لإتمام العملية.']);
        }

        // خصم الرصيد
        $user->balance -= $total;
        $user->save();

        // 1. إنشاء الطلب أولاً
$order = Order::create([
    'user_id'   => $user->id,
    'game_id'   => $request->game_id,
    'offer_id'  => $request->offer_id,
    'type'      => 'voucher',
    'player_id' => 'N/A',
    'quantity'  => $request->quantity,
    'total_price' => $total,
    'status'    => 'completed',
]);

// 2. ثم نربط الأكواد بالطلب
foreach ($codes as $code) {
    $code->is_used = true;
    $code->order_id = $order->id;  // الآن order موجود فعلاً
    $code->save();
}

        // ⭐ تسجيل المعاملة
        Transaction::create([
            'user_id'       => $user->id,
            'order_id'      => $order->id,
            'type'          => 'purchase',
            'amount'        => -$total,
            'balance_after' => $user->balance,
        ]);

        return back()->with('success', '✅ تم شراء الأكواد بنجاح!');
    }
}