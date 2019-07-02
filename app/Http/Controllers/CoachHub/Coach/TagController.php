<?php

namespace App\Http\Controllers\CoachHub\Coach;

use App\Http\Resources\Sport\SportResource;
use App\Models\CoachHub\Coach\CoachProfile;
use App\Models\CoachHub\Tag;
use App\Models\ContactRequest;
use App\Jobs\ContactRequestJob;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    private $coach;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('coach');
        $this->coach = Auth::user()->coachBaseProfile;
    }

    /**
     * Get all tags related to the coach
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tags = $this->coach->tags;

        return ['data' => $tags];
    }

    /**
     * Get all programs related to the coach
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $program)
    {
        $this->_confirmUserHasProgram($program->coach->id);

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
    public function show(Tag $program)
    {
        $this->_confirmUserHasProgram($program->coach->id);

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

        if ($program = $this->coach->programs()->create($result)) {
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
    public function update(Tag $program, Request $request)
    {
        $result = $this->_validateInput($request);

        $this->_confirmUserHasProgram($program->coach->id);

        if ($program->update($result)) {
            return ['data' => $program];
        } else {
            return response()->json('There was an error updating the program', 422);
        }
    }

    private function _confirmUserHasProgram($programId) {
        if ($programId !== $this->coach->id) {
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
                    $query->where('coach_base_profile_id', $this->coach->id);
                }),
            ]
        ]);
    }
}
