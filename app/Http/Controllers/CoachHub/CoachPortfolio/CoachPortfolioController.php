<?php

namespace App\Http\Controllers\CoachHub\CoachPortfolio;

use App\Http\Resources\CoachHub\CoachProfile\CoachProfileResource;
use App\Http\Resources\Program\ProgramResource;
use App\Models\CoachHub\Coach\CoachBaseProfile;
use App\Models\CoachHub\Coach\CoachProfile;
use App\Models\CoachHub\Program;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CoachPortfolioController extends Controller
{
    private $user;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Get all required state for coaches
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function profile($name, Request $request)
    {
        // TODO: Validate username
        $lowercaseName = strtolower($name);

        $coach = CoachBaseProfile::select('id')->where('username', $lowercaseName)->first();

        if ($coach) {
            $profiles = CoachProfile::where('coach_base_profile_id', $coach->id)->get();
            $programs = Program::search()->where('coach_base_profile_id', $coach->id)->get();

            return ['data' => [
                'id' => $lowercaseName,
                'profiles' => CoachProfileResource::collection($profiles),
                'programs' => ProgramResource::collection($programs)
            ]];
        }

        abort(404, trans('programs.not_found'));
    }
}
