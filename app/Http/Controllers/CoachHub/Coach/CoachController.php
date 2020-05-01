<?php

namespace App\Http\Controllers\CoachHub\Coach;

use App\Http\Resources\CoachHub\CoachProfile\CoachProfileResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CoachController extends Controller
{
    private $user;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
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
        $coachBaseProfile = $this->user->coachBaseProfile;

        if (!$coachBaseProfile) {
            return response()->json('No base profile, you must complete that first', 422);
        }

        $profiles = $coachBaseProfile->coachProfiles;

        return ['data' => [
            'profiles' => CoachProfileResource::collection($profiles)
        ]];
    }
    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function application(Request $request)
    {
        $request->validate([
            'sports' => 'required|integer|exists:sports,id',
            'coaching_experience' => 'required|string|min:140',
            'athletic_highlights' => 'required|string|min:140',
            'session_plan' => 'required|string|min:140',
            'one_sentence_bio' => 'required|string|max:180'
        ]);

        $coachBaseProfile = $this->user->coachBaseProfile;

        if (!$coachBaseProfile) {
            return response()->json('No base profile, you must complete that first', 422);
        }

        // Save the new coach profile to the user
        $coachProfile = $coachBaseProfile->coachProfiles()->create([
            'coaching_experience' => $request->input('coaching_experience'),
            'athletic_highlights' => $request->input('athletic_highlights'),
            'session_plan' => $request->input('session_plan'),
            'one_sentence_bio' => $request->input('one_sentence_bio')
        ]);

        // If save was successful
        if ($coachProfile) {
            // Attach the sports
            $coachProfile->sports()->sync($request->input('sports'));
            return ['data' => new CoachProfileResource($coachProfile)];
        }

        return response()->json('Could not create the profile due to an error', 422);
    }
}
