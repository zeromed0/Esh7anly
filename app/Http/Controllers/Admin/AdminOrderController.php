<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'offer', 'game'])
            ->latest()
            ->get();

        return Inertia::render('Admin/Orders', [
            'orders' => $orders
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::with('user')->findOrFail($id);
        $user = $order->user;

        $status = $request->status;
        $message = $request->message;

        // 🔒 منع تعديل الطلب بعد اكتماله
        if (in_array($order->status, ['completed', 'rejected'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order already finalized'
            ], 400);
        }

        /*
        |-----------------------------------
        | VALIDATE MESSAGE FOR TOPUP
        |-----------------------------------
        */

        if ($order->type === 'topup' && $status === 'completed') {

            $request->validate([
                'message' => 'required|string|max:255'
            ]);

            $order->message = $message;
        }

        /*
        |-----------------------------------
        | ORDER COMPLETED
        |-----------------------------------
        */

        if ($status === 'completed') {

            $order->status = 'completed';
            $order->save();

            Transaction::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'amount' => -($order->total_price ?? $order->price ?? 0),
                'type' => $order->type,
                'balance_after' => $user->balance,
                'player_id' => $order->player_id,
                'message' => $order->message
            ]);
        }

        /*
        |-----------------------------------
        | ORDER REJECTED + REFUND
        |-----------------------------------
        */

        if ($status === 'rejected') {

            $refund = $order->total_price ?? $order->price ?? 0;

            $user->balance += $refund;
            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'amount' => $refund,
                'type' => 'refund',
                'balance_after' => $user->balance,
                'message' => 'Order rejected refund'
            ]);

            $order->status = 'rejected';
            $order->save();
        }

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }
}