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
    public function index()
    {
        $user = auth()->user();

        // الرصيد (رقم)
        $balance = floatval($user->balance ?? 0);

        // عدد المعاملات المكتملة فقط (orders completed + wallet topups)
        $transactionsCount =
            Order::where('user_id', $user->id)
                ->where('status', 'completed')
                ->count()
            +
            Transaction::where('user_id', $user->id)
                ->where('type', 'wallet')
                ->count();

        // مجموع ما أنفقه اليوم (completed orders فقط)
        $usedToday = floatval(
            Order::where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->where('status', 'completed')
                ->sum('total_price')
        );

        // daily limit (يمكن لاحقًا جلبه من إعدادات أو عمود في users)
        $dailyLimit = floatval($user->daily_limit ?? 2000);

        return Inertia::render('Profile', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
                'avatar' => $user->avatar,
                'is_verified' => (bool) $user->is_verified,
                'identity_status' => $user->identity_status ?? null, // pending/approved/rejected/null
                'identity_card' => $user->identity_card ?? null,
                'selfie' => $user->selfie ?? null,
            ],
            'balance' => $balance,
            'transactionsCount' => $transactionsCount,
            'usedToday' => $usedToday,
            'dailyLimit' => $dailyLimit,
        ]);
    }

    // تحديث الاسم (basic profile)
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->name = $data['name'];
        $user->save();

        return back()->with('success', 'Profile updated.');
    }

    // تغيير كلمة المرور
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->password = Hash::make($data['password']);
        $user->save();

        return back()->with('success', 'Password changed.');
    }

    // رفع الهوية + سيلفي
    public function uploadIdentity(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'identity_card' => 'nullable|image|max:4096',
            'selfie' => 'nullable|image|max:4096',
        ]);

        // إذا لم يُرسل أي ملف، نعيد خطأ بسيط
        if (! $request->hasFile('identity_card') && ! $request->hasFile('selfie')) {
            return back()->withErrors(['identity' => 'Please provide at least one file (identity card or selfie).']);
        }

        if ($request->hasFile('identity_card')) {
            $path = $request->file('identity_card')->store('identity', 'public');
            $user->identity_card = $path;
        }

        if ($request->hasFile('selfie')) {
            $path = $request->file('selfie')->store('identity', 'public');
            $user->selfie = $path;
        }

        // وضع الحالة قيد الانتظار للمراجعة
        $user->identity_status = 'pending';
        $user->is_verified = false; // تأكيد أن التحقق لم يتم بعد
        $user->save();

        return back()->with('success', 'Documents uploaded. Waiting for review.');
    }

    // رفع صورة البروفايل
    public function uploadAvatar(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'avatar' => 'required|image|max:4096',
        ]);

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return back()->with('success', 'Avatar updated.');
    }
}