<?php

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

test('oauth confirmation state is stored when redirecting to a linked provider', function () {
    $user = User::factory()->create([
        'google_id' => 'google-123',
    ]);

    $driver = Mockery::mock();
    $driver->shouldReceive('with')
        ->once()
        ->with(['prompt' => 'select_account'])
        ->andReturnSelf();
    $driver->shouldReceive('redirect')
        ->once()
        ->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

    Socialite::shouldReceive('driver')
        ->once()
        ->with('google')
        ->andReturn($driver);

    $this->actingAs($user)
        ->get(route('google.redirect', [
            'confirm_password' => 1,
            'intended' => '/settings/two-factor',
        ]))
        ->assertRedirect('https://accounts.google.com/o/oauth2/auth')
        ->assertSessionHas('oauth_password_confirmation', function (array $state) use ($user): bool {
            return ($state['user_id'] ?? null) === $user->id
                && ($state['provider'] ?? null) === 'google'
                && ($state['intended'] ?? null) === '/settings/two-factor';
        });
});

test('oauth callback confirms password state for two factor routes', function () {
    $user = User::factory()->create([
        'google_id' => 'google-123',
    ]);

    $socialUser = new SocialiteUser;
    $socialUser->map([
        'id' => 'google-123',
        'email' => $user->email,
        'name' => $user->name,
    ]);

    $driver = Mockery::mock();
    $driver->shouldReceive('user')
        ->once()
        ->andReturn($socialUser);

    Socialite::shouldReceive('driver')
        ->once()
        ->with('google')
        ->andReturn($driver);

    $this->actingAs($user)
        ->withSession([
            'oauth_password_confirmation' => [
                'user_id' => $user->id,
                'provider' => 'google',
                'intended' => '/settings/two-factor',
            ],
        ])
        ->get(route('google.callback'))
        ->assertRedirect('/settings/two-factor')
        ->assertSessionHas('auth.password_confirmed_at')
        ->assertSessionMissing('oauth_password_confirmation');

    $this->actingAs($user)
        ->get(route('two-factor.show'))
        ->assertOk();
});

test('oauth callback cancellation during confirmation redirects back to confirm password', function () {
    $user = User::factory()->create([
        'fb_id' => 'facebook-123',
    ]);

    $this->actingAs($user)
        ->withSession([
            'oauth_password_confirmation' => [
                'user_id' => $user->id,
                'provider' => 'facebook',
                'intended' => '/settings/two-factor',
            ],
        ])
        ->get(route('facebook.callback', [
            'error' => 'access_denied',
            'error_reason' => 'user_denied',
            'error_description' => 'Permissions error',
        ]))
        ->assertRedirect(route('password.confirm'))
        ->assertSessionHasErrors('password')
        ->assertSessionMissing('oauth_password_confirmation');
});

test('oauth login redirects to two factor challenge when two factor is enabled', function () {
    $user = User::factory()->create([
        'email' => 'oauth-user@example.com',
        'google_id' => 'google-123',
        'two_factor_secret' => encrypt('secret'),
        'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-1'])),
        'two_factor_confirmed_at' => now(),
    ]);

    $socialUser = new SocialiteUser;
    $socialUser->map([
        'id' => 'google-123',
        'email' => 'oauth-user@example.com',
        'name' => $user->name,
    ]);

    $driver = Mockery::mock();
    $driver->shouldReceive('user')
        ->once()
        ->andReturn($socialUser);

    Socialite::shouldReceive('driver')
        ->once()
        ->with('google')
        ->andReturn($driver);

    $this->get(route('google.callback'))
        ->assertRedirect(route('two-factor.login'))
        ->assertSessionHas('login.id', $user->id);

    $this->assertGuest();
});
