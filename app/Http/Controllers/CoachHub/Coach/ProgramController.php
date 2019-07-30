<?php

namespace App\Http\Controllers\CoachHub\Coach;

use App\Http\Resources\Program\ProgramResource;
use App\Models\CoachHub\Program;
use App\Models\CoachHub\ProgramPrice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

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
        $this->_confirmUserHasProgram($program->coach_base_profile_id);

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
        $this->_confirmUserHasProgram($program->coach_base_profile_id);

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
            $program->prices()->createMany($result['prices']);
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

        $this->_confirmUserHasProgram($program->coach_base_profile_id);
//        $this->_validateProgramPrices($result['prices'], $program->id);

        if ($program->update($result)) {
            // $program->prices()->delete();
            foreach ($result['prices'] as $price) {
                $program->prices()->updateOrCreate(['guid' => $price['guid']], $price);
            }
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

//    private function _validateProgramPrices($prices, $programId) {
//        foreach ($prices as $price) {
//            if ($price[''])
//        }
//        if () {
//            abort(404, trans('programs.not_found'));
//        }
//    }

    private function _validateInput(Request $request) {
        $result = $request->validate([
            'program_title' => 'required|string|max:180',
            'program_description' => 'required|string|max:255',
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
            ],
            'prices' => 'array',
            'prices.*.id' => 'nullable|integer',
            'prices.*.name' => 'required|string|max:180',
            'prices.*.guid' => 'required|string|max:10',
            'prices.*.price' => 'required|numeric',
            'prices.*.capacity' => 'required|integer|min:1|max:200',
            'prices.*.has_wait_list' => 'required|boolean',
            'prices.*.sub_options' => 'nullable|array',
            'prices.*.sub_options_preset' => 'nullable|in:0,1,2',
            // TODO: Figure out a max
            'prices.*.multi_sub_options_required' => 'nullable|integer|min:2|max:10',
        ]);

        $prices = [];
        foreach ($result['prices'] as $price) {
            // Store the parent capacity
            $capacity = $price['capacity'];
            // Store the parent sub options array
            $subOptions = $price['sub_options'];

            // If the root price has 0 sub options
            if (count($subOptions) == 0) {
                $parent = [
                    'name' => $price['name'],
                    'guid' => $price['guid'],
                    'price' => $price['price'],
                    'capacity' => $capacity,
                    'has_wait_list' => $price['has_wait_list'],
                    'sub_options' => [],
                    'sub_options_preset' => null,
                    'multi_sub_options_required' => null
                ];

                if ($price['id']) {
                    $parent['id'] = $price['id'];
                }

                // Add the parent option to the array of ProgramPrices
                array_push($prices, $parent);
            } else {

                $parentSubOptions = [];
                // Store the root price's amount of multi sub options required
                $multiSubOptionsRequired = $price['multi_sub_options_required'];
                // Store the root price's sub option preset
                $subOptionsPreset = $price['sub_options_preset'];

                $parent = [
                    'name' => $price['name'],
                    'guid' => $price['guid'],
                    'price' => $price['price'],
                    'capacity' => $capacity,
                    'has_wait_list' => $price['has_wait_list']
                ];

                if ($price['id']) {
                    $parent['id'] = $price['id'];
                }

                // If the sub option preset is equal to 0
                // Therefore (non required)
                if ($subOptionsPreset == 0) {
                    $parent['multi_sub_options_required'] = null;
                    // If the sub option preset is equal to 1
                    // Therefore (one required)
                } else if ($subOptionsPreset == 1) {
                    // Make sure there is at least 2 sub options
                    if (count($subOptions) > 1) {
                        $parent['multi_sub_options_required'] = null;
                    } else {
                        // throw error
                    }
                } else if ($subOptionsPreset == 2) {
                    // Make sure the amount required is less than the amount of sub options
                    if ($multiSubOptionsRequired < count($subOptions)) {
                        $parent['multi_sub_options_required'] = $multiSubOptionsRequired;
                    } else {
                        // throw error
                    }
                }

                // A present must be set when there are sub options
                if ($subOptionsPreset == null) {
                    $parent['sub_options_preset'] = 0;
                } else {
                    $parent['sub_options_preset'] = $subOptionsPreset;
                }

                foreach ($subOptions as $subOption) {
                    $subOptionResult = Validator::make($subOption, [
                        'id' => 'nullable|integer',
                        'name' => 'required|string|max:180',
                        'guid' => 'required|string|max:10',
                        'price' => 'required|numeric',
                        // Make sure sub option capacity has a max of the root option capacity
                        'capacity' => 'required|integer|min:1|max:' . $price['capacity'],
                        'has_wait_list' => 'required|boolean',
                    ])->validate();

                    $guid = $subOption['guid'];

                    $sp = [
                        'name' => $subOptionResult['name'],
                        'guid' => $guid,
                        'price' => $subOptionResult['price'],
                        'capacity' => $subOption['capacity'],
                        'has_wait_list' => $subOptionResult['has_wait_list'],
                        // All suboptions will forcefully have the below null values
                        'sub_options' => null,
                        'sub_options_preset' => null,
                        'multi_sub_options_required' => null
                    ];

                    if ($subOptionResult['id']) {
                        $sp['id'] = $subOptionResult['id'];
                    }
                    // Add the guid to the parentSubOptions array
                    array_push($parentSubOptions, $guid);
                    // Add the sub option to the main array of prices related to the program
                    array_push($prices, $sp);
                }

                // Add the array of subOption guids to the parent option
                $parent['sub_options'] = $parentSubOptions;
                // Add the parent option to the start of the array of ProgramPrices
                array_unshift($prices, $parent);
            }
        }

        $result['prices'] = $prices;

        return $result;
    }
}
