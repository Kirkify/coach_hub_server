<?php

namespace App\Http\Controllers\CoachHub\Coach;

use App\Http\Resources\CoachHub\Location\LocationResource;
use App\Models\CoachHub\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
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
     * Get all locations
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function index(Request $request)
    {
        $items = $this->coach->locations;

        return ['data' => LocationResource::collection($items)];
    }

    /**
     * Get a specific location
     *
     * @param  \App\Models\CoachHub\Location  $location
     * @return array
     */
    public function show(Location $location)
    {
        $this->_confirmUserOwnsItem($location->coach->id);

        return ['data' => new LocationResource($location)];
    }

    /**
     * Handle a create request for an item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function store(Request $request)
    {
        $result = $this->_validateInput($request);

        if ($item = $this->coach->locations()->create($result)) {
            return ['data' => new LocationResource($item)];
        } else {
            return response()->json(trans('crud.store_error'), 422);
        }
    }

    /**
     * Handle an update request for an item.
     *
     * @param  \App\Models\CoachHub\Location  $location
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function update(Location $location, Request $request)
    {
        $result = $this->_validateInput($request);

        $this->_confirmUserOwnsItem($location->coach->id);

        if ($location->update($result)) {
            return ['data' => new LocationResource($location)];
        } else {
            return response()->json(trans('crud.update_error'), 422);
        }
    }

    /**
     * Handle a destroy request for an item.
     *
     * @param  \App\Models\CoachHub\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function destroy(Location $location, Request $request)
    {
        $this->_confirmUserOwnsItem($location->coach->id);

        if ($location->delete()) {
            return response()->json();
        } else {
            return response()->json(trans('crud.delete_error'), 422);
        }
    }

    private function _confirmUserOwnsItem($ownerId) {
        if ($ownerId !== $this->coach->id) {
            abort(404, trans('crud.not_found'));
        }
    }

    private function _validateInput(Request $request) {
        return $request->validate([
            'name' => 'required|string|max:180',
            'description' => 'nullable|string',
            'street_number' => 'required|string',
            'street_name' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'postal_code' => 'required|string',
            'notes' => 'nullable|string'
        ]);
    }
}
