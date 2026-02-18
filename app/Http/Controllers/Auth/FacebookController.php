<?php

namespace App\Http\Controllers\Auth;
use Laravel\Socialite\Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FacebookController extends Controller
{
    //
    public function redirectToFacebook()
    {
        // This method will redirect the user to the Facebook authentication page.
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        // This method will handle the callback from Facebook after authentication.
        $user = Socialite::driver('facebook')->user();

        // Here you can handle the user information and create or log in the user as needed.
        // For example, you can check if the user already exists in your database and log them in,
        // or create a new user record if they don't exist.

        // After handling the user information, you can redirect them to the desired page.

        dd($user); // For debugging purposes, you can dump the user information to see what you get from Facebook.
    }
}
