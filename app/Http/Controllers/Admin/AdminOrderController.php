<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'offer', 'game'])
            ->orderBy('id', 'desc')
            ->get();

        return Inertia::render('Admin/Orders', [
            'orders' => $orders,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::with('user')->findOrFail($id);
        $user = $order->user;
        $status = $request->input('status');

        // 🔒 منع تعديل الطلب بعد الاكتمال أو الرفض
        if (in_array($order->status, ['completed', 'rejected'])) {
            return back()->with('error', 'لا يمكن تعديل هذا الطلب بعد اكتماله أو رفضه.');
        }

        // ✅ عند الاكتمال فقط نغير الحالة
        if ($status === 'completed') {
            $order->status = 'completed';
            $order->save();
        }

        // 🔁 عند الرفض: إعادة المبلغ للمستخدم وتسجيل العملية
        elseif ($status === 'rejected') {
            // استخدم total_price إن وجد، وإلا السعر العادي
            $refundAmount = $order->total_price ?? $order->price ?? 0;

            // أضف المبلغ إلى رصيد المستخدم
            $user->balance += $refundAmount;
            $user->save();

            // سجل المعاملة في transactions
            Transaction::create([
                'user_id' => $user->id,
                'amount' => $refundAmount,
                'type' => 'refund',
                'balance_after' => $user->balance,
            ]);

            // حدّث حالة الطلب
            $order->status = 'rejected';
            $order->save();
        }

        return back()->with('success', 'تم تحديث حالة الطلب بنجاح.');
    }
}