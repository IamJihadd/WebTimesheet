<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        // // DEBUG: Log data yang diterima
        // Log::info('Login attempt:', [
        //     'user_id' => $this->user_id,
        //     'has_password' => !empty($this->password),
        //     'remember' => $this->boolean('remember'),
        //     'all_input' => $this->all()
        // ]);

        $this->ensureIsNotRateLimited();

        // Login pakai user_id dan check is_active
        if (! Auth::attempt([
            'user_id' => $this->user_id,
            'password' => $this->password,
            'is_active' => true
        ], $this->boolean('remember'))) {

            // DEBUG: Log kenapa gagal
            // Log::error('Login failed for user_id: ' . $this->user_id);

            // RateLimiter::hit($this->throttleKey());

            // throw ValidationException::withMessages([
            //     'user_id' => trans('auth.failed'),
            // ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'user_id' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('user_id')) . '|' . $this->ip());
    }
}
