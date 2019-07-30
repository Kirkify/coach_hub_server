<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'email_only_secret' => env('EMAIL_ONLY_SECRET'),

    'google_recaptcha' => env('GOOGLE_RECAPTCHA_SECRET'),

    'web_client_id' => env('WEB_CLIENT_ID'),

    'web_client_secret' => env('WEB_CLIENT_SECRET'),

];
