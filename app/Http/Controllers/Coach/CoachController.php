<?php

namespace App\Http\Controllers\Coach;

use App\Http\Resources\CoachProfile\CoachProfileResource;
use App\Http\Resources\Sport\SportResource;
use App\Models\CoachProfile;
use App\Models\ContactRequest;
use App\Jobs\ContactRequestJob;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CoachController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function initialState(Request $request)
    {
        $user = $request->user();
        $coachProfile = $user->coachProfile;

        $sports = Sport::all();

        return ['data' => [
            'sports' => SportResource::collection($sports),
            'coachProfile' => $coachProfile ? new CoachProfileResource($coachProfile) : null
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
            'sports' => 'required|array',
            'sports.*' => 'exists:sports,id',
            'coaching_experience' => 'required|string|min:140',
            'athletic_highlights' => 'required|string|min:140',
            'session_plan' => 'required|string|min:140',
            'one_sentence_bio' => 'required|string|max:180'
        ]);

        $user = $request->user();
        $coachProfile = $user->coachProfile;

        if ($coachProfile) {
            return response()->json('A Coach Profile already exists for you', 422);
        } else {
            $userProfile = $user->userProfile;

            // Before completing a coach profile the user must have a user profile
            if ($userProfile) {
                // Save the new coach profile to the user
                $coachProfile = $user->coachProfile()->create([
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
            } else {
                return response()->json(
                    'You must create a user profile before you can apply to become a coach',
                    422
                );
            }
        }
        return response()->json();
    }
}
