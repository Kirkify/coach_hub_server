<?php

namespace App\Http\Controllers\Auth;

use App\Models\ConfirmEmail;
use App\Rules\Captcha;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    use IssueTokenTrait;

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request, AccessTokenController $accessTokenController)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|confirmed|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'captcha' => ['required', new Captcha]
        ]);

        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => strtolower($request->input('email')),
            'password' => Hash::make($request->input('password')),
            'verified' => 0
        ]);

        $confirmEmail = new ConfirmEmail(['token' => md5(random_bytes(5))]);

        $user->confirmEmail()->save($confirmEmail);

        event(new Registered($user));

        return response()->json();
    }

    public function verify(Request $request) {

        $request->validate([
            'email' => 'required|string|email|max:255',
            'token' => 'required|string|size:32',
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (!is_null($user)) {
            $confirmEmail = $user->confirmEmail;

            if (!is_null($confirmEmail)) {
                $token = $confirmEmail->token;

                // Make sure the submitted token is equal to the one saved on their account
                if ($token == $request->input('token')) {
                    // Let's update their email verification status
                    $user->verified = 1;
                    $user->confirmEmail()->delete();
                    $user->save();
                    event(new Registered($user));
                    return $this->issueToken($request, 'social');
                } else {
                    return response()->json(trans('password.confirmation_code'), 422);
                }
            } else {
                return response()->json(trans('auth.email_already_verified'), 422);
            }
        }
        return response()->json(trans('passwords.user'), 422);
    }
}
