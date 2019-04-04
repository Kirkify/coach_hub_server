<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserProfile\CoachProfileResource;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api']);
    }

    public function getProfile(Request $request)
    {
        return $request->user()->profile;
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string|max:255'
        ]);

        $user = $request->user();

        if (Hash::check($request->input('password'), $user->password))
        {

        } else {
            return response()->json('Your password was incorrect', 422);
        }
//        $user->first_name = $request->input('first_name');
//        $user->last_name = $request->input('last_name');
//
//        if ($user->save())
//        {
//            return response()->json($user);
//        }

        return response()->json('There was an error updating your name', 422);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $user = $request->user();

        if (Hash::check($request->input('current_password'), $user->password))
        {
            $user->password = Hash::make($request->input('password'));
            if ($user->save())
            {
                return response()->json();
            }
        } else {
            return response()->json('Your password was incorrect', 422);
        }

        return response()->json('There was an error updating your password', 422);
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

        return response()->json('There was an error updating your name', 422);
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
            if ($userProfile->update($request->all())) {
                return ['data' => new CoachProfileResource($userProfile)];
            }
        } else {
            $profile = new UserProfile($request->all());
            if ($user->profile()->save($profile)) {
                return ['data' => new CoachProfileResource($profile)];
            }
        }

        return response()->json('There was an error updating your profile', 422);
    }
}
