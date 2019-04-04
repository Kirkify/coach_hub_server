<?php

namespace App\Http\Controllers;

use App\Models\ContactRequest;
use App\Jobs\ContactRequestJob;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function contact(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone_number' => 'nullable|regex:[\(\b[0-9]{3}\)[" ". ][0-9]{3}[-. ][0-9]{4}\b]',
            'message' => 'string',
            'prefer_call' => 'boolean'
        ]);

        // If user is currently logged in let's relate their user_id to the contact request
        $userId = Auth::guard('api')->check() ? Auth::guard('api')->id() : null;
        $request->request->add(['user_id' => $userId]);

        $user = ContactRequest::create($request->all());

        ContactRequestJob::dispatch($user);

        return response()->json();
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        // TODO: should 'scope' be added to this validation
        return Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone_number' => 'nullable|regex:[\(\b[0-9]{3}\)[" ". ][0-9]{3}[-. ][0-9]{4}\b]',
            'message' => 'string',
            'prefer_call' => 'boolean'
        ]);
    }
}
