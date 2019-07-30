<?php

namespace App\Http\Controllers\CoachHub\Coach;

use App\Events\CoachHub\RegistrationEvent;
use App\Http\Resources\CoachHub\Registration\RegistrationResource;
use App\Models\CoachHub\Registration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RegistrationController extends Controller
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
        $registrations = $this->coach->registrations;

        return ['data' => RegistrationResource::collection($registrations)];
    }

    /**
     * Get all programs related to the coach
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function destroy(Registration $registration)
    {
        $this->_confirmUserHasProgram($registration->coach_base_profile_id);

        if ($registration->delete()) {
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
    public function show(Registration $registration)
    {
        $this->_confirmUserHasProgram($registration->coach_base_profile_id);

        return ['data' => new RegistrationResource($registration)];
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

        // TODO: An event should be sent out to notify coach of registration
        if ($registration = $this->coach->registrations()->create($result)) {
            $response = (new RegistrationResource($registration))->resolve();
            event(new RegistrationEvent($this->coach->user->id, $response));
            return ['data' => $response];
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
    public function update(Registration $registration, Request $request)
    {
        $result = $this->_validateInput($request);

        $this->_confirmUserHasProgram($registration->coach_base_profile_id);

        if ($registration->update($result)) {
            return ['data' => new RegistrationResource($registration)];
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
            'first_name' => 'required|string|max:180',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'date_of_birth' => 'required|date',
            'notes' => 'nullable|string',
            'program_id' => [
                'required',
                'integer',
                Rule::exists('programs', 'id')->where(function ($query) {
                    $query->where('coach_base_profile_id', $this->coach->id);
                }),
            ]
        ]);
    }
}
