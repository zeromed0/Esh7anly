<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $notifications = Order::where('user_id', $user->id)
            ->where('type', 'topup')
            ->where('status', 'completed')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'message' => 'TopUp completed for Player ID: ' . $order->player_id,
                    'details' => $order->message,
                    'created_at' => $order->created_at,
                ];
            });

        return response()->json($notifications);
    }
}