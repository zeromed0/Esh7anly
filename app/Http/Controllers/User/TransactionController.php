<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\VoucherCode;

class TransactionController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // معاملات Wallet
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
                    'created_at' => $tx->created_at,
                ];
            });

        // معاملات orders (voucher + topup)
        $orderTx = Order::where('user_id', $user->id)
            ->with(['game', 'offer', 'voucherCodes'])
            ->latest()
            ->get()
            ->map(function ($order) {

                $codes = $order->voucherCodes
                    ->pluck('code')
                    ->toArray();

                // جلب آخر رصيد بعد تنفيذ هذا الطلب
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
                    'created_at' => $order->created_at,
                ];
            });

        // دمج الكل
        $transactions = $walletTx
            ->concat($orderTx)
            ->sortByDesc('created_at')
            ->values();

        return Inertia::render('Transactions', [
            'user' => $user,
            'transactions' => $transactions,
        ]);
    }
}