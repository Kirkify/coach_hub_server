<?php

namespace App\Http\Controllers\Coach;

use App\Http\Resources\CoachProfile\CoachProfileResource;
use App\Http\Resources\Sport\SportResource;
use App\Models\CoachProfile;
use App\Models\ContactRequest;
use App\Jobs\ContactRequestJob;
use App\Models\Program;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProgramController extends Controller
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
     * Get all programs related to the coach
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $programs = $this->user->programs;

        return ['data' => $programs];
    }

    /**
     * Get all programs related to the coach
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function destroy(Program $program)
    {
        $this->_confirmUserHasProgram($program->user->id);

        if ($program->delete()) {
            return response()->json();
        } else {
            return response()->json('There was an error deleting the program', 422);
        }
    }

    /**
     * Get all programs related to the coach
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function show(Program $program)
    {
        $this->_confirmUserHasProgram($program->user->id);

        return ['data' => $program];
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $result = $this->_validateInput($request);

        if ($program = $this->user->programs()->create($result)) {
            return ['data' => $program];
        } else {
            return response()->json('There was an error creating the program', 422);
        }
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function update(Program $program, Request $request)
    {
        $result = $this->_validateInput($request);

        $this->_confirmUserHasProgram($program->user->id);

        if ($program->update($result)) {
            return ['data' => $program];
        } else {
            return response()->json('There was an error updating the program', 422);
        }
    }

    private function _confirmUserHasProgram($programId) {
        if ($programId !== $this->user->id) {
            abort(404, trans('programs.not_found'));
        }
    }

    private function _validateInput(Request $request) {
        return $request->validate([
            'program_title' => 'required|string|max:180',
            'program_description' => 'required|string',
            'registration_start' => 'required|date|after_or_equal:today',
            'registration_end' => 'required|date|after_or_equal:registration_start',
            'program_start' => 'required|date|after_or_equal:today',
            'program_end' => 'required|date|after_or_equal:program_start',
            'location_id' => [
                'required',
                'integer',
                Rule::exists('locations', 'id')->where(function ($query) {
                    $query->where('user_id', $this->user->id);
                }),
            ]
        ]);
    }
}
