<?php

namespace App\Http\Controllers\CoachHub\Coach;

use App\Http\Resources\Program\ProgramResource;
use App\Models\CoachHub\Program;
use App\Models\CoachHub\ProgramPrice;
use App\Models\FormHub\Form;
use Carbon\Carbon;
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
        $programs = $this->coach->programs()->with('form')->get();

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
    public function store()
    {
        // TODO: By default we are setting the below dates, listen to customer feedback and update
        $today = Carbon::today();
        $default = [
            'registration_start' => $today,
            'registration_end' => $today->copy()->addDays(7),
            'program_start' => $today->copy()->addDays(7),
            'program_end' => $today->copy()->addDays(10)
        ];
        if ($program = $this->coach->programs()->create($default)) {
            // $program->prices()->createMany($result['prices']);
            // $program->tags()->sync($result['tags']);
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
    public function addForm(Program $program, Request $request)
    {
        $this->_confirmUserHasProgram($program->coach_base_profile_id);

        $input = $request->validate([
            'form_id' => 'required|string',
        ]);

        $form = Form::where('id', $input['form_id'])->users()->get();

        if ($program->update($input)) {
            return response()->json();
        }
        return response()->json('There was an error updating the program', 422);

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
            return ['data' => new ProgramResource($program->fresh())];
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
            'program_title' => 'nullable|string|max:180',
            'program_description' => 'nullable|string|max:255',
            'category' => [
                'nullable',
                'integer',
//                Rule::exists('locations', 'id')->where(function ($query) {
//                    $query->where('user_id', $this->user->id);
//                }),
            ],
            'registration_start' => 'nullable|date',
            'registration_end' => 'nullable|date|after_or_equal:registration_start',
            'program_start' => 'nullable|date',
            'program_end' => 'nullable|date|after_or_equal:program_start',
            'location_id' => [
                'nullable',
                'integer',
                Rule::exists('locations', 'id')->where(function ($query) {
                    $query->where('coach_base_profile_id', $this->coach->id);
                }),
            ],
            'tags' => 'nullable|array',
            'tags.*' => [
                'integer',
                Rule::exists('tags', 'id')->where(function ($query) {
                    $query->where('coach_base_profile_id', $this->coach->id);
                }),
            ],
            'prices' => 'nullable|array',
            'prices.*.id' => 'nullable|integer',
            'prices.*.name' => 'nullable|string|max:180',
            'prices.*.guid' => 'required|string|max:10',
            'prices.*.price' => 'required|numeric',
            'prices.*.capacity' => 'required|integer|min:1|max:200',
            'prices.*.has_wait_list' => 'required|boolean',
            'prices.*.sub_options' => 'nullable|array',
            'prices.*.sub_options_preset' => 'nullable|in:0,1,2',
            // TODO: Figure out a max
            'prices.*.multi_sub_options_required' => 'nullable|integer|min:2|max:10',
        ]);

        $rootPrices = array_filter($result['prices'], function($element) {
            return is_array($element['sub_options']);
        });

        $allSubOptions = array_values(array_filter($result['prices'], function($element) {
            return $element['sub_options'] === null;
        }));

        $finalPrices = [];

        foreach ($rootPrices as $price) {
            // Store the parent capacity
            $capacity = $price['capacity'];
            // Store the parent sub options array
            $subOptionGuids = $price['sub_options'];
            // Store the count of subOptions
            $subOptionCount = count($subOptionGuids);

            // If the root price has 0 sub options
            if ($subOptionCount === 0) {
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

                if (array_key_exists('id', $price)) {
                    $parent['id'] = $price['id'];
                }

                // Add the parent option to the array of ProgramPrices
                array_push($finalPrices, $parent);
            } else {

                // Store the root price's sub option preset
                $subOptionsPreset = $price['sub_options_preset'];

                $parent = [
                    'name' => $price['name'],
                    'guid' => $price['guid'],
                    'price' => $price['price'],
                    'capacity' => $capacity,
                    'has_wait_list' => $price['has_wait_list'],
                    'sub_options' => $subOptionGuids,
                    'sub_options_preset' => $subOptionsPreset,
                ];

                // Store the root price's amount of multi sub options required
                $multiSubOptionsRequired = $price['multi_sub_options_required'];

                // A preset must be set when there are sub options
                if ($subOptionsPreset === null) {
                    // throw error

                // If one is required
                } else if ($subOptionsPreset == 1) {
                    // Make sure there is at least 2 sub options
                    if ($subOptionCount <= 1) {
                        // throw error
                    }
                    $parent['multi_sub_options_required'] = null;
                } else if ($subOptionsPreset == 2) {
                    // Make sure the amount required is less than the amount of sub options
                    if ($subOptionCount >= $multiSubOptionsRequired) {
                        // throw error
                    }
                    $parent['multi_sub_options_required'] = $multiSubOptionsRequired;
                }

                // Add the parent option to the array of ProgramPrices
                array_push($finalPrices, $parent);

                foreach ($subOptionGuids as $subOptionGuid) {
                    $key = array_search($subOptionGuid, array_column($allSubOptions, 'guid'));

                    if ($key !== false) {
                        $subOption = $allSubOptions[$key];

                        // For some reason a capacity greater than the parent has been set
                        if ($subOption['capacity'] > $capacity) {
                            // throw error
                        }

                        $sp = [
                            'name' => $subOption['name'],
                            'guid' => $subOption['guid'],
                            'price' => $subOption['price'],
                            'capacity' => $subOption['capacity'],
                            'has_wait_list' => $subOption['has_wait_list'],
                            // All suboptions will forcefully have the below null values
                            'sub_options' => null,
                            'sub_options_preset' => null,
                            'multi_sub_options_required' => null
                        ];

                        array_push($finalPrices, $sp);

                    // A subOption could not be found
                    } else {
                        // throw error
                    }
                }
            }
        }

        $result['prices'] = $finalPrices;

        return $result;
    }
}
