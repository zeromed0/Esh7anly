<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $user  = $order->user;

        $request->validate([
            'status'  => 'required|in:completed,rejected',
            'message' => 'nullable|string|max:255'
        ]);

        if (in_array($order->status, ['completed', 'rejected'])) {
            return back();
        }

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | ✅ COMPLETE ORDER
            |--------------------------------------------------------------------------
            */

            if ($request->status === 'completed') {

                if ($order->type === 'topup' && !$request->message) {
                    DB::rollBack();
                    return back();
                }

                if ($order->type === 'topup') {
                    $order->message = $request->message;
                }

                $order->status = 'completed';
                $order->save();

                Transaction::create([
                    'user_id'       => $user->id,
                    'order_id'      => $order->id,
                    'amount'        => -($order->total_price ?? $order->price ?? 0),
                    'type'          => $order->type,
                    'balance_after' => $user->balance,
                    'player_id'     => $order->player_id,
                    'message'       => $order->message
                ]);

                DB::commit();
                return back();
            }

            /*
            |--------------------------------------------------------------------------
            | ❌ REJECT ORDER + REFUND
            |--------------------------------------------------------------------------
            */

            if ($request->status === 'rejected') {

                $refund = $order->total_price ?? $order->price ?? 0;

                $user->balance += $refund;
                $user->save();

                Transaction::create([
                    'user_id'       => $user->id,
                    'order_id'      => $order->id,
                    'amount'        => $refund,
                    'type'          => 'refund',
                    'balance_after' => $user->balance,
                    'message'       => 'Order rejected - refund processed'
                ]);

                $order->status = 'rejected';
                $order->save();

                DB::commit();
                return back();
            }

        } catch (\Exception $e) {

            DB::rollBack();
            return back();
        }
    }
}