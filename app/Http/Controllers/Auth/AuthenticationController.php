<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Lcobucci\JWT\Parser as JwtParser;

class AuthenticationController extends Controller
{
    use IssueTokenTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['logout']]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'nullable|string'
        ]);

        return $this->issueToken($request, 'refresh_token');
    }

    /**
     * Handle a login request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|max:255'
        ]);

        return $this->issueToken($request, 'password');
    }

    /**
     * Handle a logout request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->token()->revoke();
            // TODO: Add Client Id Web check
            return response()->json()->withCookie(Cookie::forget('refreshToken'));
        }

        return response()->json();
    }
}
