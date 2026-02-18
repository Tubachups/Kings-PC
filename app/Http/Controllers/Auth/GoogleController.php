<?php

namespace App\Http\Controllers\Auth;
use Laravel\Socialite\Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    //
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
{
    $googleUser = Socialite::driver('google')->user();

    $user = User::firstOrCreate(
        ['email' => $googleUser->getEmail()],
        [
            'name' => $googleUser->getName(),
            'google_id' => $googleUser->getId(),
            'password' => bcrypt(Str::random(24)), // random password
        ]
    );

    // Update google_id if user exists but doesn't have it yet
    if (!$user->google_id) {
        $user->google_id = $googleUser->getId();
        $user->save();
    }

    Auth::login($user);

    return redirect()->route('dashboard');
}
}
