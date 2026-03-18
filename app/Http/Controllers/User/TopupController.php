<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Game;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class TopupController extends Controller
{
    public function index()
    {
        // جلب جميع الألعاب من نوع topup مع العروض المرتبطة
        $games = Game::where('type', 'topup')->with('offers')->get();

        // جلب الضريبة من جدول الإعدادات
        $taxPercent = Setting::first()?->tax_percent ?? 0;

        return Inertia::render('Topup', [
            'games'       => $games,
            'user'        => Auth::user(),
            'tax_percent' => (float)$taxPercent,
        ]);
    }

    
public function store(Request $request)
{
    $request->validate([
        'game_id'   => 'required|exists:games,id',
        'offer_id'  => 'required|exists:offers,id',
        'player_id' => 'required|string|max:50',
        'quantity'  => 'required|integer|min:1',
    ]);

    $user = Auth::user();

    $game = Game::findOrFail($request->game_id);
    $offer = Offer::findOrFail($request->offer_id);

    if (!$game->is_active || !$offer->is_active) {
        return back()->withErrors(['error' => 'هذا العرض أو اللعبة غير مفعل.']);
    }

    $taxPercent = Setting::get('tax_percent', 10);

    $baseTotal = $offer->price * $request->quantity;
    $tax = !$user->is_premium_active ? $baseTotal * ($taxPercent / 100) : 0;
    $total = $baseTotal + $tax;

    /*
    |------------------------------------
    | limit for unverified users
    |------------------------------------
    */

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

    /*
    |------------------------------------
    | الرصيد
    |------------------------------------
    */

    if ($user->balance < $total) {
        return back()->withErrors(['wallet_balance' => 'رصيدك غير كافٍ لإتمام العملية.']);
    }

    if (!Setting::get('topup_enabled', true)) {
        return back()->withErrors(['error' => 'عمليات الشحن مغلقة حالياً.']);
    }

    $user->balance -= $total;
    $user->save();

    $order = Order::create([
        'user_id'     => $user->id,
        'game_id'     => $game->id,
        'offer_id'    => $offer->id,
        'type'        => 'topup',
        'player_id'   => $request->player_id,
        'quantity'    => $request->quantity,
        'total_price' => $total,
        'tax'         => $tax,
        'status'      => 'pending',
    ]);

    Transaction::create([
        'user_id'       => $user->id,
        'order_id'      => $order->id,
        'type'          => 'topup',
        'amount'        => -$total,
        'balance_after' => $user->balance,
        'details'       => 'Topup purchase' . ($tax > 0 ? " (+tax $tax)" : ''),
    ]);

    return back()->with('success', '✅ تم إرسال طلبك وهو الآن قيد المراجعة.');
}
}