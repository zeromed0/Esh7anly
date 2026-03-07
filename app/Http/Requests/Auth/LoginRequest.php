<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // تحقق أولاً من أن المستخدم موجود وليس محظور
        $user = User::where('email', $this->input('email'))->first();

        if (!$user || !password_verify($this->input('password'), $user->password)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        if ($user->is_banned) {
            throw ValidationException::withMessages([
                'email' => __('هذا الحساب معطل. لا يمكنك تسجيل الدخول.'),
            ]);
        }

        // تسجيل الدخول إذا لم يكن محظور
        Auth::login($user, $this->boolean('remember'));

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => RateLimiter::availableIn($this->throttleKey()),
                'minutes' => ceil(RateLimiter::availableIn($this->throttleKey()) / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }
}