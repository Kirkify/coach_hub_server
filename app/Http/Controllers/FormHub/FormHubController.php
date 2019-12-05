<?php

namespace App\Http\Controllers\FormHub;

use App\Models\FormHub\Form;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FormHubController extends Controller
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
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function createNewForm(Request $request)
    {
        $uuid = Str::uuid();
        $form = Form::create([
            'id' => $uuid,
            'value' => null
        ]);
        $this->user->forms()->attach($uuid->toString());
        return ['data' => $form];
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function getForm(Form $form, Request $request)
    {
        if ($form->isUserOwner($this->user)) {
            return ['data' => $form];
        }
        return response()->json('Error Getting Form', 422);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function updateForm(Form $form, Request $request)
    {
        if ($form->isUserOwner($this->user)) {
            $input = $request->validate([
                'value' => 'required|string',
            ]);
            $form->update($input);
            return ['data' => $form];
        }

        return response()->json('Error Updating', 422);
    }
}
