<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * عرض صفحة البروفايل مع بيانات المستخدم
     */
    public function index()
    {
        $user = auth()->user();

        // الرصيد الحالي للمستخدم
        $balance = floatval($user->balance ?? 0);

        // عدد المعاملات المكتملة: orders + wallet topups
        $transactionsCount =
            Order::where('user_id', $user->id)
                ->where('status', 'completed')
                ->count()
            +
            Transaction::where('user_id', $user->id)
                ->where('type', 'wallet')
                ->count();

        // مجموع ما أنفقه المستخدم اليوم (completed orders فقط)
        $usedToday = floatval(
            Order::where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->where('status', 'completed')
                ->sum('total_price')
        );

        return Inertia::render('Profile', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? null,
                'national_id' => $user->national_id ?? null,
                'created_at' => $user->created_at,
                'is_premium' => (bool) $user->is_premium,
                'premium_until' => $user->premium_until ?? null,
                'verification_status' => $user->verification_status, // unverified/pending/verified
                'identity_status' => $user->identity_status ?? null, // pending/approved/rejected/null
                'id_card_image' => $user->id_card_image ?? null,
                'selfie_with_id_image' => $user->selfie_with_id_image ?? null,
            ],
            'balance' => $balance,
            'transactionsCount' => $transactionsCount,
            'usedToday' => $usedToday,
        ]);
    }
}