<?php


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class VerifyEmailController extends Controller
{
    /**
     * إعادة توليد رابط التحقق وإرساله للمستخدم.
     */
    public function resend($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->hasVerifiedEmail()) {
            return "البريد الإلكتروني بالفعل مفعل.";
        }

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',        // يجب أن يكون نفس اسم الراوت في web.php
            Carbon::now()->addDay(),      // صلاحية الرابط: يوم كامل
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        // إرسال الرابط عبر البريد (يمكنك تعديل قالب الرسالة)
        Mail::raw("اضغط هنا لتأكيد بريدك: $verificationUrl", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('تأكيد البريد الإلكتروني');
        });

        return "تم إرسال رابط تحقق جديد بنجاح!";
    }
}