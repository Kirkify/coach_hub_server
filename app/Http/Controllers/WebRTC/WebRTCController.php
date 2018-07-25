<?php

namespace App\Http\Controllers\WebRTC;

use App\Events\WebRTCAcceptConnectionRequest;
use App\Events\WebRTCConnectionRequest;
use App\Events\WebRTCNewIceCandidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;

class WebRTCController extends Controller
{
    private $appId;
    private $authKey;
    private $url;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->appId = config('socket.apps.default');
        $this->authKey = config('socket.auth_key');
        $this->url = 'http://laravel-echo-server:6001/apps/' . $this->appId;
    }

    /**
     * Sends all outstanding and need to see information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function getAllChannels(Request $request)
    {
        // TODO: This method should only be available to admins
        $http = new Client();

        $response = $http->get($this->url . '/channels?auth_key=' . $this->authKey);

        if ($response->getStatusCode() === 200) {
            return $response->getBody();
        } else {
            return response()->json('Error fetching channels', 422);
        }
    }

    /**
     * Sends all outstanding and need to see information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function connectWithUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'message' => 'required|string'
        ]);

        $userIdOfFriend = $request->input('user_id');
        $message = $request->input('message');
        $user = $request->user();

        // TODO: Maybe add check to make sure user is online as well
        if ($user->hasFriend($userIdOfFriend)) {
            event(new WebRTCConnectionRequest($user, $userIdOfFriend, $message));
            return response()->json('Connection Request Sent');
        } else {
            return response()->json(trans('passwords.please_verify_email'), 422);
        }
    }

    /**
     * Sends all outstanding and need to see information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function acceptConnectionRequest(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'message' => 'required|string'
        ]);

        $userIdOfFriend = $request->input('user_id');
        $message = $request->input('message');
        $user = $request->user();

        // TODO: Maybe add check to make sure user is online as well
        if ($user->hasFriend($userIdOfFriend)) {
            event(new WebRTCAcceptConnectionRequest($user, $userIdOfFriend, $message));
            return response()->json('Connection Request Sent');
        } else {
            return response()->json(trans('passwords.please_verify_email'), 422);
        }
    }

    /**
     * Sends all outstanding and need to see information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function sendIceCandidate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'message' => 'required|string'
        ]);

        $userIdOfFriend = $request->input('user_id');
        $message = $request->input('message');
        $user = $request->user();

        // TODO: Maybe add check to make sure user is online as well
        if ($user->hasFriend($userIdOfFriend)) {
            event(new WebRTCNewIceCandidate($user, $userIdOfFriend, $message));
            return response()->json('Connection Request Sent');
        } else {
            return response()->json(trans('passwords.please_verify_email'), 422);
        }
    }
// acceptConnectionRequest
}
