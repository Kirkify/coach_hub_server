<?php

namespace App\Http\Controllers\CoachHub;

use App\Http\Resources\CoachHub\Coach\CoachBaseProfileResource;
use App\Http\Resources\CoachHub\CoachProfile\CoachProfileResource;
use App\Http\Resources\Sport\SportResource;
use App\Models\CoachHub\Coach\CoachBaseProfile;
use App\Models\CoachHub\Sport;
use App\Rules\Username;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CoachHubController extends Controller
{
    private $user;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->user = Auth::user();
    }

    /**
     * Get all required state for coaches
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function initialState(Request $request)
    {
        // If user is currently logged in let's relate their user_id to the contact request
//        $user = Auth::guard('api')->check() ? Auth::guard('api') : null;
        $coachBaseProfile = $this->user->coachBaseProfile;

//        if ($user) {
//            $coachBaseProfile = $this->user->coachBaseProfile;
//        }

        $sports = Sport::all();

        return ['data' => [
            'sports' => SportResource::collection($sports),
            'coachBaseProfile' => $coachBaseProfile ? new CoachBaseProfileResource($coachBaseProfile) : null
        ]];
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function createCoachBaseProfile(Request $request)
    {
        $input = $request->validate([
            'name' => 'required|string|max:100',
            'username' => [ 'required', new Username, "unique:coach_base_profiles" ],
            'gender' => 'required|in:m,f,o',
            'date_of_birth' => 'required|date',
        ]);

        $coachProfile = $this->user->coachBaseProfile;

        if ($coachProfile) {
            return response()->json('A Coach Profile already exists for you', 422);
        } else {
            // Save the new coach profile to the user
            $coachProfile = $this->user->coachBaseProfile()->create($input);

            // If save was successful
            if ($coachProfile) {
                return ['data' => new CoachBaseProfileResource($coachProfile)];
            }
        }
        return response()->json('Could not create the profile due to an error', 422);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function updateCoachBaseProfile(CoachBaseProfile $profile, Request $request)
    {
        if ($profile->user->id !== $this->user->id) {
            abort(404, trans('programs.not_found'));
        }

        $input = $request->validate([
            'name' => 'required|string|max:100',
            'gender' => 'required|in:m,f,o',
            'date_of_birth' => 'required|date',
        ]);

        // If save was successful
        if ($profile->update($input)) {
            return ['data' => new CoachBaseProfileResource($profile)];
        }

        return response()->json('Could not create the profile due to an error', 422);
    }
}
