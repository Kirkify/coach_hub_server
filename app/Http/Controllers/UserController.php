<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api']);
    }

    public function getProfile(Request $request)
    {
        $profile = $request->user()->profile;
        if ($profile)
        {
            return $profile;
        }
        // TODO: do something else here if no profile
        return $profile;
    }

    public function updateUser(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255'
        ]);

        $user = $request->user();

        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');

        if ($user->save())
        {
            return response()->json($user);
        }

        return response()->json('There was an error updating your account', 401);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'phone_number' => 'nullable|regex:[\(\b[0-9]{3}\)[" ". ][0-9]{3}[-. ][0-9]{4}\b]',
            'date_of_birth' => 'nullable|date_format:"Y-m-d',
            'gender' => 'nullable|in:m,f,u',
            'street_number' => 'nullable|string',
            'street_name' => 'nullable|string',
            'apt_number' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'postal_code' => 'nullable|string',
        ]);

        $user = $request->user();
        $userProfile = $user->profile;

        if ($userProfile) {
            $userProfile->update($request->all());
        } else {
            $profile = new UserProfile($request->all());
            $user->profile()->save($profile);
        }

        return response()->json();
    }
}
