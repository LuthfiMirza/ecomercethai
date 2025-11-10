<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function redirect(Request $request)
    {
        $redirectUrl = localized_route('social.google.callback', parameters: [], absolute: true);

        return Socialite::driver('google')
            ->redirectUrl($redirectUrl)
            ->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $redirectUrl = localized_route('social.google.callback', parameters: [], absolute: true);
            $googleUser = Socialite::driver('google')
                ->redirectUrl($redirectUrl)
                ->stateless()
                ->user();
        } catch (\Exception $exception) {
            return redirect()->route('login', ['locale' => app()->getLocale()])
                ->with('error', __('Unable to authenticate with Google. Please try again.'));
        }

        if (! $googleUser || ! $googleUser->getEmail()) {
            return redirect()->route('login', ['locale' => app()->getLocale()])
                ->with('error', __('We could not retrieve your Google email address.'));
        }

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Google User',
                'email_verified_at' => now(),
                'password' => bcrypt(Str::random(32)),
            ]
        );

        Auth::login($user, true);

        return redirect()->intended(route('home', ['locale' => app()->getLocale()]));
    }
}
