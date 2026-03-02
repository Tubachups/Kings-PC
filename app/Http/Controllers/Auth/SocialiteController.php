<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        $this->ensureSupportedProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        $this->ensureSupportedProvider($provider);

        $socialUser = Socialite::driver($provider)->user();
        $email = $socialUser->getEmail();
        $providerId = $socialUser->getId();

        if (! is_string($email) || $email === '') {
            return redirect()->route('login')->withErrors([
                'socialite' => ucfirst($provider).' did not return an email address.',
            ]);
        }

        if (! is_string($providerId) || $providerId === '') {
            return redirect()->route('login')->withErrors([
                'socialite' => ucfirst($provider).' did not return an account ID.',
            ]);
        }

        $user = User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => $socialUser->getName() ?: $email,
                'password' => Hash::make(Str::random(24)),
            ]
        );

        if ($provider === 'google' && $user->google_id !== $providerId) {
            $user->forceFill(['google_id' => $providerId])->save();
        }

        if ($provider === 'facebook' && $user->fb_id !== $providerId) {
            $user->forceFill(['fb_id' => $providerId])->save();
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    private function ensureSupportedProvider(string $provider): void
    {
        abort_unless(in_array($provider, ['google', 'facebook'], true), 404);
    }
}
