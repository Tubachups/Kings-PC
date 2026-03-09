<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Fortify\Events\TwoFactorAuthenticationChallenged;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialiteController extends Controller
{
    public function redirect(Request $request, string $provider): RedirectResponse
    {
        $this->ensureSupportedProvider($provider);

        if ($this->isOauthPasswordConfirmationRequest($request, $provider)) {
            $this->storeOauthPasswordConfirmationState($request, $provider);
        }

        $driver = Socialite::driver($provider);

        if ($provider === 'google' || $provider === 'facebook') {
            $driver = $driver->with([
                'prompt' => 'select_account',
            ]);
        }

        return $driver->redirect();
    }

    public function callback(Request $request, string $provider): RedirectResponse
    {
        $this->ensureSupportedProvider($provider);

        if ($request->filled('error')) {
            return $this->handleProviderCallbackError($request, $provider);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable) {
            if ($request->session()->has('oauth_password_confirmation')) {
                $request->session()->forget('oauth_password_confirmation');

                return redirect()->route('password.confirm')->withErrors([
                    'password' => 'Unable to verify your social login. Please try again.',
                ]);
            }

            return redirect()->route('login')->withErrors([
                'socialite' => ucfirst($provider).' sign in failed. Please try again.',
            ]);
        }

        if ($request->session()->has('oauth_password_confirmation')) {
            return $this->handleOauthPasswordConfirmation($request, $provider, $socialUser);
        }

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

        if ($this->shouldChallengeTwoFactorAuthentication($user)) {
            $request->session()->put([
                'login.id' => $user->getKey(),
                'login.remember' => false,
            ]);

            TwoFactorAuthenticationChallenged::dispatch($user);

            return redirect()->route('two-factor.login');
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    private function handleOauthPasswordConfirmation(Request $request, string $provider, SocialiteUser $socialUser): RedirectResponse
    {
        /** @var array{user_id?: int|string, provider?: string, intended?: string}|mixed $confirmation */
        $confirmation = $request->session()->pull('oauth_password_confirmation');

        $authenticatedUser = $request->user();

        if (
            ! is_array($confirmation) ||
            ! $authenticatedUser ||
            (int) ($confirmation['user_id'] ?? 0) !== (int) $authenticatedUser->getKey()
        ) {
            return redirect()->route('login');
        }

        if (($confirmation['provider'] ?? null) !== $provider) {
            return redirect()->route('password.confirm')->withErrors([
                'password' => 'Please confirm your linked social account again.',
            ]);
        }

        $providerId = $socialUser->getId();
        $expectedProviderId = $provider === 'google' ? $authenticatedUser->google_id : $authenticatedUser->fb_id;

        if (
            ! is_string($providerId) ||
            $providerId === '' ||
            ! is_string($expectedProviderId) ||
            $expectedProviderId === '' ||
            ! hash_equals($expectedProviderId, $providerId)
        ) {
            return redirect()->route('password.confirm')->withErrors([
                'password' => 'Unable to verify your social login account.',
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->to($this->sanitizeIntendedUrl($confirmation['intended'] ?? null));
    }

    private function handleProviderCallbackError(Request $request, string $provider): RedirectResponse
    {
        $isDeniedByUser = $request->query('error') === 'access_denied'
            || $request->query('error_reason') === 'user_denied';

        if ($request->session()->has('oauth_password_confirmation')) {
            $request->session()->forget('oauth_password_confirmation');

            return redirect()->route('password.confirm')->withErrors([
                'password' => $isDeniedByUser
                    ? 'Social login confirmation was cancelled.'
                    : 'Unable to verify your social login. Please try again.',
            ]);
        }

        return redirect()->route('login')->withErrors([
            'socialite' => $isDeniedByUser
                ? ucfirst($provider).' sign in was cancelled.'
                : ucfirst($provider).' sign in failed. Please try again.',
        ]);
    }

    private function isOauthPasswordConfirmationRequest(Request $request, string $provider): bool
    {
        $user = $request->user();

        if (! $request->boolean('confirm_password') || ! $user?->isOauthUser()) {
            return false;
        }

        $providerId = $provider === 'google' ? $user->google_id : $user->fb_id;

        return is_string($providerId) && $providerId !== '';
    }

    private function storeOauthPasswordConfirmationState(Request $request, string $provider): void
    {
        $request->session()->put('oauth_password_confirmation', [
            'user_id' => $request->user()?->id,
            'provider' => $provider,
            'intended' => $this->sanitizeIntendedUrl($request->string('intended')->toString()),
        ]);
    }

    private function sanitizeIntendedUrl(?string $intended): string
    {
        if (! is_string($intended) || $intended === '') {
            return route('two-factor.show');
        }

        $path = parse_url($intended, PHP_URL_PATH);
        $query = parse_url($intended, PHP_URL_QUERY);

        if (! is_string($path) || $path === '' || ! str_starts_with($path, '/')) {
            return route('two-factor.show');
        }

        return is_string($query) && $query !== '' ? $path.'?'.$query : $path;
    }

    private function shouldChallengeTwoFactorAuthentication(User $user): bool
    {
        if (! Features::enabled(Features::twoFactorAuthentication())) {
            return false;
        }

        if (! in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user), true)) {
            return false;
        }

        if (Fortify::confirmsTwoFactorAuthentication()) {
            return filled($user->two_factor_secret) && ! is_null($user->two_factor_confirmed_at);
        }

        return filled($user->two_factor_secret);
    }

    private function ensureSupportedProvider(string $provider): void
    {
        abort_unless(in_array($provider, ['google', 'facebook'], true), 404);
    }
}
