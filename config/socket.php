<?php

return [

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
            'default' => 'default.' // This will always be followed with the Users Id
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
