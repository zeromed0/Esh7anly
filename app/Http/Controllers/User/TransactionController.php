<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\Transaction;
use App\Models\Order;

class TransactionController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        /*
        |--------------------------------
        | Wallet Transactions
        |--------------------------------
        */

        $walletTx = Transaction::where('user_id', $user->id)
            ->where('type', 'wallet')
            ->latest()
            ->get()
            ->map(function ($tx) {
                return (object)[
                    'id' => $tx->id,
                    'type' => 'wallet',
                    'status' => 'completed',
                    'game_name' => '-',
                    'offer_name' => '-',
                    'quantity' => '-',
                    'total_price' => $tx->amount,
                    'balance_after' => $tx->balance_after,
                    'codes' => [],
                    'player_id' => '-',
                    'message' => $tx->details ?? null,
                    'created_at' => $tx->created_at,
                ];
            });

        /*
        |--------------------------------
        | Orders Transactions
        |--------------------------------
        */

        $orderTx = Order::where('user_id', $user->id)
            ->with(['game', 'offer', 'voucherCodes'])
            ->latest()
            ->get()
            ->map(function ($order) {

                $codes = $order->voucherCodes
                    ->pluck('code')
                    ->toArray();

                // جلب آخر رصيد بعد الطلب
                $lastBalance = Transaction::where('user_id', $order->user_id)
                    ->where('created_at', '<=', $order->created_at)
                    ->latest()
                    ->value('balance_after');

                return (object)[
                    'id' => $order->id,
                    'type' => $order->type,
                    'status' => $order->status,
                    'game_name' => $order->game->name ?? '-',
                    'offer_name' => $order->offer->name ?? '-',
                    'quantity' => $order->quantity,
                    'total_price' => $order->total_price,
                    'balance_after' => $lastBalance,
                    'codes' => $codes,
                    'player_id' => $order->player_id,
                    'message' => $order->message, // ← هنا الحل
                    'created_at' => $order->created_at,
                ];
            });

        /*
        |--------------------------------
        | Merge Transactions
        |--------------------------------
        */

        $transactions = $walletTx
            ->concat($orderTx)
            ->sortByDesc('created_at')
            ->values();

        return Inertia::render('Transactions', [
            'user' => $user,
            'transactions' => $transactions
        ]);
    }
}