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
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    public function index()
    {
        $games = Game::where('type', 'voucher')
            ->with(['offers.voucherCodes' => function($query) {
                $query->where('is_used', false); // فقط الأكواد غير المستعملة
            }])
            ->get();

        // أضف عدد الأكواد لكل عرض فقط غير المستعملة
        $games->map(function ($game) {
            $game->offers->map(function ($offer) {
                $offer->stock = $offer->voucherCodes->count();
                return $offer;
            });
            return $game;
        });

        // جلب الضريبة من جدول settings
    $taxPercent = Setting::first()?->tax_percent ?? 0;

        return Inertia::render('Vouchers', [
            'games' => $games,
            'user' => Auth::user(),
            'tax_percent' => (float)$taxPercent,
        ]);
    }

    
public function store(Request $request)
{
    $request->validate([
        'game_id' => 'required|exists:games,id',
        'offer_id' => 'required|exists:offers,id',
        'quantity' => 'required|integer|min:1',
    ]);

    $user = Auth::user();
    $offer = Offer::findOrFail($request->offer_id);

    // تحقق من توفر الأكواد
    $codes = VoucherCode::where('offer_id', $offer->id)
        ->where('is_used', false)
        ->take($request->quantity)
        ->get();

    if ($codes->count() < $request->quantity) {
        return back()->withErrors(['error' => '❌ لا توجد أكواد كافية لهذا العرض.']);
    }

    // جلب الضريبة
    $taxPercent = Setting::get('tax_percent', 10);
    $baseTotal = $offer->price * $request->quantity;
    $tax = (!$user->is_premium_active) ? $baseTotal * ($taxPercent / 100) : 0;
    $total = $baseTotal + $tax;

    // فحص الرصيد
    if ($user->balance < $total) {
        return back()->withErrors(['error' => 'رصيدك غير كافٍ لإتمام العملية.']);
    }

    if ($user->verification_status !== 'verified') {

    $todayTotal = Order::where('user_id', $user->id)
        ->whereDate('created_at', now())
        ->whereIn('status', ['pending','completed'])
        ->sum('total_price');

    if (($todayTotal + $total) > 1500) {

        return back()->withErrors([
            'limit' => '❌ الحد اليومي للمستخدم غير الموثق هو 1500 MRU. يرجى توثيق حسابك.'
        ]);

    }
}

    // خصم الرصيد
    $user->balance -= $total;
    $user->save();

    // إنشاء الطلب
    $order = Order::create([
        'user_id'     => $user->id,
        'game_id'     => $request->game_id,
        'offer_id'    => $request->offer_id,
        'type'        => 'voucher',
        'player_id'   => 'N/A',
        'quantity'    => $request->quantity,
        'total_price' => $total,
        'tax'         => $tax,
        'status'      => 'completed',
    ]);

    // ربط الأكواد بالطلب
    foreach ($codes as $code) {
        $code->is_used = true;
        $code->order_id = $order->id;
        $code->save();
    }

    // تسجيل المعاملة
    Transaction::create([
        'user_id'       => $user->id,
        'order_id'      => $order->id,
        'type'          => 'purchase',
        'amount'        => -$total,
        'balance_after' => $user->balance,
        'details'       => 'Voucher purchase' . ($tax > 0 ? " (+tax $tax)" : ''),
    ]);

    // هنا نرجع الأكواد مباشرة للـ Vue عبر Inertia
    return inertia('Vouchers', [
        'games'       => Game::where('type', 'voucher')->with('offers')->get(),
        'user'        => $user,
        'tax_percent' => $taxPercent,
        'codes'       => $codes->pluck('code')->toArray(), // <- الأكواد الفعلية
    ]);
}
}