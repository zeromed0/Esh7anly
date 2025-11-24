<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Game;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TopupController extends Controller
{
    public function index()
    {
        $games = Game::where('type', 'topup')->with('offers')->get();

        return Inertia::render('Topup', [
            'games' => $games,
            'user' => Auth::user(),
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
        $offer = Offer::findOrFail($request->offer_id);
        $total = $offer->price * $request->quantity;

        // فحص الرصيد
        if ($user->balance < $total) {
            return back()->withErrors(['wallet_balance' => 'رصيدك غير كافٍ لإتمام العملية.']);
        }

        // خصم الرصيد
        $user->balance -= $total;
        $user->save();

        // إنشاء الطلب
        $order = Order::create([
            'user_id'     => $user->id,
            'game_id'     => $request->game_id,
            'offer_id'    => $request->offer_id,
            'type'        => 'topup',
            'player_id'   => $request->player_id,
            'quantity'    => $request->quantity,
            'total_price' => $total,
            'status'      => 'pending',
        ]);

        // ⭐ تسجيل معاملة الشحن
        Transaction::create([
            'user_id'       => $user->id,
            'order_id'      => $order->id,
            'type'          => 'topup',
            'amount'        => -$total,
            'balance_after' => $user->balance,
        ]);

        return back()->with('success', '✅ تم إرسال طلبك وهو الآن قيد المراجعة.');
    }
}