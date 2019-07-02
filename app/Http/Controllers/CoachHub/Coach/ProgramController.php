<?php

namespace App\Http\Controllers\CoachHub\Coach;

use App\Http\Resources\Program\ProgramResource;
use App\Models\CoachHub\Program;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProgramController extends Controller
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
     * Get all programs related to the coach
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $programs = $this->coach->programs;

        return ['data' => ProgramResource::collection($programs)];
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
        $this->_confirmUserHasProgram($program->coach->id);

        if ($program->delete()) {
            return response()->json();
        } else {
            return response()->json('There was an error deleting the program', 422);
        }
    }

    /**
     * Get specific program related to a coach
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function show(Program $program)
    {
        $this->_confirmUserHasProgram($program->coach->id);

        return ['data' => new ProgramResource($program)];
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
            $program->tags()->sync($result['tags']);
            return ['data' => new ProgramResource($program)];
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

        $this->_confirmUserHasProgram($program->coach->id);

        if ($program->update($result)) {
            $program->tags()->sync($result['tags']);
            return ['data' => new ProgramResource($program)];
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
            'category' => [
                'nullable',
                'integer',
//                Rule::exists('locations', 'id')->where(function ($query) {
//                    $query->where('user_id', $this->user->id);
//                }),
            ],
            'registration_start' => 'required|date',
            'registration_end' => 'required|date|after_or_equal:registration_start',
            'program_start' => 'required|date',
            'program_end' => 'required|date|after_or_equal:program_start',
            'max_participants' => 'required|integer|min:1|max:100',
            'has_wait_list' => 'required|boolean',
            'location_id' => [
                'required',
                'integer',
                Rule::exists('locations', 'id')->where(function ($query) {
                    $query->where('coach_base_profile_id', $this->coach->id);
                }),
            ],
            'tags' => 'array',
            'tags.*' => [
                'integer',
                Rule::exists('tags', 'id')->where(function ($query) {
                    $query->where('coach_base_profile_id', $this->coach->id);
                }),
            ]
        ]);
    }
}
