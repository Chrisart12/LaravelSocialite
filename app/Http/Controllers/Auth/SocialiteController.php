<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect($provider)
    {

        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
    
        try {
            $socialUser = Socialite::driver($provider)->user();

            if (User::where('email', $socialUser->getEmail())->exists()) {
                return redirect('/login')->withErrors((['email' => 'This email uses different method to login']));
            }

            $user = user::where([
                'provider' => $provider,
                'provider_id' => $socialUser->id,
            ])->first();

            if (!$user) {
                $user = user::create([
                    'provider' => $provider,
                    'provider_id' => $socialUser->id,
                    'firstname' => $socialUser->nickname ? $socialUser->nickname : $socialUser->name,
                    'email' => $socialUser->email,
                    'provider_token' => $socialUser->token,
                    'email_verified_at' => now(),
                ]);
            }

            Auth::login($user);

            return redirect('/dashboard');

        } catch (\Throwable $th) {
            return redirect('/login');
        }

        // // dd($socialUser);
        // $user = User::updateOrCreate([
        //     'provider_id' => $socialUser->id,
        //     'provider' => $provider,
        // ], [
        //     'firstname' => $socialUser->nickname ? $socialUser->nickname : $socialUser->name,
        //     'email' => $socialUser->email,
        //     'provider_token' => $socialUser->token,
        // ]);
     
        Auth::login($user);
     
        return redirect('/dashboard');
    }

    
}
