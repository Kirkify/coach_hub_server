<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Lcobucci\JWT\Parser;
use App\Models\User;

trait IssueTokenTrait
{

    public function issueToken(Request $request, $grantType)
    {
        $request->validate([
            'client_id' => 'nullable|integer',
            'client_secret' => 'nullable|string|max:50',
            'scope' => 'nullable|string',
            'email' => 'nullable|email',
            'password' => 'nullable|string|max:255',
        ]);

        // If user has supplied a client id, client secret and scope, use it,
        // Else use the default Web Client keys and default '*' scope
        $clientId = $request->input('client_id', env('WEB_CLIENT_ID'));
        $clientSecret = $request->input('client_secret', env('WEB_CLIENT_SECRET'));
        $scope = $request->input('scope', '*');

        $params = [
            'grant_type' => $grantType,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'scope' => $scope,
            'username' => $request->input('email'),
            'password' => $request->input('password'),
            'refresh_token' => $request->input('refresh_token')
        ];

        $request->request->add($params);

        // If refresh_token grant and issued through our web app
        if ($grantType == 'refresh_token' && $clientId === env('WEB_CLIENT_ID')) {
            // Grab the token from the cookie
            $refreshToken = $request->cookie('refreshToken');
            $request->request->add(['refresh_token' => $refreshToken]);
        }

        // If refresh_token grant and issued through our web app
        if ($grantType == 'email_only' && $clientId === env('WEB_CLIENT_ID')) {
            // Grab the token from the cookie
            $request->request->add(['email_only_secret' => env('EMAIL_ONLY_SECRET')]);
        }

        $proxy = Request::create('oauth/token?XDEBUG_SESSION_START=18195', 'POST');

        $response = Route::dispatch($proxy);

        if ($response->isSuccessful()) {
            // Add user object to every token request
            $jwt = json_decode($response->getContent());
            // We can grab the user id from the access token
            $accessToken = (new Parser())->parse($jwt->access_token);
            $userId = $accessToken->getClaim('sub');

            // TODO: Make one query which returns the user roles as well
            // This could be further improved if eventually adding user and role info to token
            $user = User::find($userId);

            if ($user->verified) {
                $jwt->user = $user;
                $jwt->roles = $user->getRoleNames();

                // If issued through the Web
                if ($clientId === env('WEB_CLIENT_ID')) {

                    // We set the refresh_token as an HTTP Only cookie
                    $response->cookie(
                        'refreshToken',
                        $jwt->refresh_token,
                        config('auth.tokens.expiry.refresh_token'), // 30 days
                        null,
                        null,
                        false,
                        true // HttpOnly
                    );
                    unset($jwt->refresh_token);
                }

                $response->setContent(json_encode($jwt));
            } else {
                // TODO: we should probably revoke the token
                return response()->json(trans('passwords.please_verify_email'), 401);
            }
        }
        return $response;
    }
}