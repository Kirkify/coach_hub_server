<?php

return [

    'apps' => [
        'default' => env('ECHO_SERVER_DEFAULT_APP', '')
    ],

    'auth_key' => env('ECHO_SERVER_AUTH_KEY', ''),
    /*
    |--------------------------------------------------------------------------
    | Broadcast Channels
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the broadcast channels which clients can listen on
    | By default all sockets are funneled through the default channel
    |
    */

    'channels' => [
        'private' => [
            'default' => 'default.' // . will always be followed with the Users Id
        ],
        'presence' => [
            'default' => 'default'
        ],
        'public' => [

        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Broadcast Events
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the broadcast events which clients can listen for
    | By default we listen for the default event and through a switch statement can sift through
    | The payload to decide where it should go (This allows us to solely only listen on 1 channel for 1 event)
    |
    */

    'events' => [
        'default' => 'default'
    ],
];
