<?php

namespace App\Http\Controllers\Auth;

use App\Rules\Captcha;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;

class RegisterController extends Controller
{
    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request, AccessTokenController $accessTokenController)
    {
        $this->registrationValidator($request->all())->validate();

        $user = $this->create($request->all());

        event(new Registered($user));

        return response()->json();

        // We want to force the user to verify their email before logging in
        /*
        $request->request->add(['grant_type' => 'password']);
        $request->request->add(['scope' => '*']);
        $request->request->add(['username' => $request['email']]);

        $psr7Request = (new DiactorosFactory)->createRequest($request);

        $token = $accessTokenController->issueToken($psr7Request);

        if ($token->getStatusCode() == 200)
        {
            // Add user object to every token request
            // TODO: investigate if this is a security issue
            $tokenContent = json_decode($token->getContent());
            $tokenContent->user = $user;
            $token->setContent(json_encode($tokenContent));
        }

        return $token;
        */
    }

    public function verify(Request $request) {
        $this->verificationValidator($request->all())->validate();

        $user = User::where('email', $request['email'])->first();

        if (!is_null($user)) {
            if ($user->verified) {
                return response()->json();
            } else {
                if ($user->email_token == $request['token']) {
                    $user->verified = 1;
                    $user->email_token = null;
                    $user->save();
                    return response()->json();
                }
            }
        }
        return response()->json(trans('passwords.email_verify_fail'), 422);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function verificationValidator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|string|email|max:255',
            'token' => 'required|string|size:32',
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function registrationValidator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|confirmed|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'captcha' => ['required', new Captcha]
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'email_token' => md5(random_bytes(5))
        ]);
    }
}
