<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Game;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // جلب الألعاب
        $games = Game::with('offers')->get();

        // ألعاب Topup و Voucher
        $topups = $games->where('type', 'topup')->take(10)->values();
        $vouchers = $games->where('type', 'voucher')->take(10)->values();

        
        // آخر الطلبات مع معلومات كاملة
        $recentOrders = Order::with(['game', 'offer'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'game_name' => $order->game->name ?? 'Unknown Game',
                    'offer_name' => $order->offer->name ?? 'Unknown Offer',
                    'type' => $order->type,
                    'total_price' => $order->total_price,
                    'status' => $order->status,
                    'date' => $order->created_at->format('Y-m-d H:i'),
                ];
            });

        return Inertia::render('Dashboard', [
            'user' => $user,
            'games' => $games,
            'topups' => $topups,
            'vouchers' => $vouchers,
            'recentOrders' => $recentOrders,
        ]);
    }
}