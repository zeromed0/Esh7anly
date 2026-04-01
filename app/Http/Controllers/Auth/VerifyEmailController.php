<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * تحقق البريد عند الضغط على الرابط.
     */
    public function __invoke(EmailVerificationRequest $request)
{
    $request->fulfill();
    return redirect('/dashboard?verified=1');
}
}