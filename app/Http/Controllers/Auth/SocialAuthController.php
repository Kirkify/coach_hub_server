<?php

namespace App\Http\Controllers\Auth;

use App\Models\SocialAccount;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Mockery\Exception;
use TheIconic\NameParser\Parser;
use Lcobucci\JWT\Parser as JwtParser;

class SocialAuthController extends Controller
{
    use IssueTokenTrait;

    public function authenticate(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'provider' => 'required|in:facebook,github,google,linkedin'
        ]);

        $provider = $request->input('provider');

        $accessToken = Socialite::driver($provider)->getAccessTokenResponse($request->input('code'));

        // If we have an access token
        if (array_key_exists('access_token', $accessToken)) {
            // Grab the user
            $socialUser = Socialite::driver($provider)->userFromToken($accessToken['access_token']);

            $email = $socialUser->email;

            // Some providers like facebook, don't force users to sign up with an email, they can also
            // use a phone number, at this point in time we do not allow this
            if ($email) {
                // Some providers like LinkedIn don't make emails lowercase
                $email = strtolower($email);
                // This param is needed for the social grant
                $request->request->add(['email' => $email]);
                // We are also forcing * scope
                // TODO: Maybe * isn't right
                $request->request->add(['scope' => '*']);
                // Check if user already has an email associated to their account
                $user = User::where('email', $email)->first();
                // If they do
                if ($user) {
                    // Issue them a token
                    return $this->issueToken($request, 'social');
                // This is their first time using this email
                } else {
                    try {
                        // Try and parse the name into a first and last
                        $parser = new Parser();
                        $names = $parser->parse($socialUser->name);
                        // Then create them a new user
                        User::create([
                            'first_name' => $names->getFirstname(),
                            'last_name' => $names->getLastname(),
                            'email' => $email,
                            'verified' => 1
                        ]);
                        // Now issue them a token
                        return $this->issueToken($request, 'social');
                    } catch (\Exception $exception) {
                        // TODO: Log exception
                    }
                }
            }
        }

        return response()->json(trans('auth.social_code', ['provider' => $request->input('provider')]), 422);
    }
}
