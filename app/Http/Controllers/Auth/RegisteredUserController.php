<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * عرض صفحة التسجيل
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * تنفيذ التسجيل
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users',
            'password'    => ['required', 'confirmed', Rules\Password::defaults()],
            'phone'       => 'required|string|max:20|unique:users',
            'national_id' => 'required|string|max:50|unique:users',
        ]);

        // إنشاء المستخدم
        $user = User::create([
            'name'                => $request->name,
            'email'               => $request->email,
            'phone'               => $request->phone,
            'national_id'         => $request->national_id,
            'verification_status' => 'unverified',
            'password'            => Hash::make($request->password),
        ]);

        // 🔥 إرسال إيميل التحقق
        event(new Registered($user));

        // تسجيل الدخول
        Auth::login($user);

        // 🔥 التوجيه لصفحة التحقق بدل dashboard
        return redirect()->route('verification.notice');
    }
}