<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Lcobucci\JWT\Parser as JwtParser;

class AuthenticationController extends Controller
{
    const REFRESH_TOKEN = 'refreshToken';
    private $accessTokenController;
    private $jwt;

    /**
     * Create a new controller instance.
     *
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @param  \Lcobucci\JWT\Parser as JwtParser  $jwt
     * @return void
     */
    public function __construct(AccessTokenController $accessTokenController, JwtParser $jwt)
    {
        $this->middleware('auth:api', ['only' => ['logout']]);
        $this->accessTokenController = $accessTokenController;
        $this->jwt = $jwt;
    }

    /**
     * TODO: what I do
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  bool $isRefreshing
     * @return \Illuminate\Http\Response
     */
    private function getNewToken(Request $request, $isRefreshing = false) {

        $request->validate([
            'client_id' => 'nullable|integer',
            'client_secret' => 'nullable|string|max:50',
            'scope' => 'nullable|string'
        ]);

        // If user has supplied a client id, client secret and scope, use it,
        // Else use the default Web Client keys and default '*' scope
        $clientId = $request->input('client_id', env('WEB_CLIENT_ID'));
        $clientSecret = $request->input('client_secret', env('WEB_CLIENT_SECRET'));
        $scope = $request->input('scope', '*');

        // Add or Override them using above input
        $request->request->add(['client_id' => $clientId]);
        $request->request->add(['client_secret' => $clientSecret]);
        $request->request->add(['scope' => $scope]);

        if ($isRefreshing) {
            $request->request->add(['grant_type' => 'refresh_token']);

            // If issued through the Web
            if ($clientId === env('WEB_CLIENT_ID')) {
                $request->request->add(['refresh_token' => $request->cookie(self::REFRESH_TOKEN)]);
                $refreshToken = $request->cookie(self::REFRESH_TOKEN);
            }
        } else {
            $request->request->add(['grant_type' => 'password']);
        }

        $psr7Request = (new DiactorosFactory)->createRequest($request);

        $response = $this->accessTokenController->issueToken($psr7Request);

        if ($response->isSuccessful()) {
            // Add user object to every token request
            // TODO: investigate if this is a security issue
            $tokenContent = json_decode($response->getContent());

            // We can grab the user id from the access token
            $accessToken = $this->jwt->parse($tokenContent->access_token);
            $userId = $accessToken->getClaim('sub');
            $user = User::find($userId);

            if ($user->verified) {
                $tokenContent->user = $user;

                // If issued through the Web
                if ($clientId === env('WEB_CLIENT_ID')) {
                    // We set the refresh_token as an HTTP Only cookie
                    $response->cookie(
                        self::REFRESH_TOKEN,
                        $tokenContent->refresh_token,
                        864000, // 10 days
                        null,
                        null,
                        false,
                        true // HttpOnly
                    );
                    unset($tokenContent->refresh_token);
                }

                $response->setContent(json_encode($tokenContent));
            } else {
                // TODO: we should probably revoke the token
                return response()->json(trans('passwords.please_verify_email'), 401);
            }
        }

        return $response;
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

        return $this->getNewToken($request, true);
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
            'email' => 'required|string|max:255',
            'password' => 'required|string|max:255'
        ]);

        $request->request->add(['username' => $request['email']]);

        return $this->getNewToken($request, false);
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

            return response()->json()->withCookie(Cookie::forget(self::REFRESH_TOKEN));
        }

        return response()->json();
    }
}
