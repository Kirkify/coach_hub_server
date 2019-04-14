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

class LocationController extends Controller
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
     * Get all locations related to the coach
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request)
    {
        $user = $request->user();
        $locations = $user->locations;

        return ['data' => $locations];
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $request->validate([
            'location_title' => 'required|string|max:180',
            'program_description' => 'nullable|string',
            'street_number' => 'required|string',
            'street_name' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'postal_code' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $user = $request->user();

        $location = $user->locations()->create($request->all());

        return ['data' => $location];
    }
}
