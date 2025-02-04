<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/contact', 'ContactController@contact')->name('contact');

Route::get('/dashboard', 'Dashboard\DashboardController@index')->name('dashboard');

Route::group(['prefix' => '/form-hub', 'namespace' => 'FormHub'], function () {
    Route::post('/new', 'FormHubController@createNewForm');
    Route::get('/get/{form}', 'FormHubController@getForm');
    Route::post('/update/{form}', 'FormHubController@updateForm');
});

Route::group(['prefix' => '/coach-hub', 'namespace' => 'CoachHub'], function () {
    Route::get('/initial-state', 'CoachHubController@initialState');
    Route::post('/coach-profile', 'CoachHubController@createCoachBaseProfile');
    Route::post('/coach-profile/{profile}', 'CoachHubController@updateCoachBaseProfile');

    Route::group(['middleware' => ['role:super-admin|coach'], 'prefix' => '/coach', 'namespace' => 'Coach'], function () {
        Route::get('/initial-state', 'CoachController@initialState');
        Route::post('/apply-to-coach', 'CoachController@application');
        Route::apiResource('locations', 'LocationController');
        Route::post('/programs/add-form/{program}', 'ProgramController@addForm');
        Route::apiResource('programs', 'ProgramController');
        Route::apiResource('tags', 'TagController');
        Route::apiResource('registrations', 'RegistrationController');
    });

    Route::group(['prefix' => '/coaches', 'namespace' => 'CoachPortfolio'], function () {
        Route::get('/{name}', 'CoachPortfolioController@profile');
    });

    Route::group(['prefix' => '/search', 'namespace' => 'Search'], function () {
        Route::get('coaches', 'SearchController@coachesIndex');
        Route::get('programs', 'SearchController@programsIndex');
    });

    Route::group(['prefix' => '/programs', 'namespace' => 'Search'], function () {
        Route::get('{program}', 'SearchController@program');
    });
});

// Messaging Routes
Route::group(['prefix' => '/messaging'], function () {
    Route::get('/contacts', 'MessagingController@contacts');
    Route::get('/unread-count', 'MessagingController@unreadCount');
    Route::get('/threads', 'MessagingController@threads');
    Route::get('/thread/{thread}', 'MessagingController@thread');
    Route::post('/thread/{thread}', 'MessagingController@threadReply');
    Route::get('/thread/mark-as-read/{thread}', 'MessagingController@markAsRead');
    Route::get('/thread/mark-as-unread/{thread}', 'MessagingController@markAsUnread');
    Route::post('/compose', 'MessagingController@compose');
});

// User Modification Routes
Route::group(['prefix' => '/user'], function () {
    Route::post('/', 'UserController@updateUser');
    Route::get('/profile', 'UserController@getProfile');
    Route::post('/profile', 'UserController@updateProfile');
    Route::post('/email', 'UserController@updateEmail');
    Route::post('/password', 'UserController@updatePassword');
});

// Authentication Routes
Route::post('/logout', 'Auth\AuthenticationController@logout')->name('logout');
Route::post('/register', 'Auth\RegisterController@register')->name('register');
Route::post('/verify', 'Auth\RegisterController@verify')->name('verify');
Route::post('/forgot-password', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('/reset-password', 'Auth\ResetPasswordController@reset');

// Login Routes (OAuth Clients Included)
Route::group(['prefix' => '/login'], function () {
    Route::post('', 'Auth\AuthenticationController@login');
    Route::post('/refresh', 'Auth\AuthenticationController@refresh')->name('refresh');
    // Social Media Authentication
    Route::post('/social', 'Auth\SocialAuthController@authenticate');
});

Route::group(['prefix' => '/webrtc'], function () {
    Route::get('get-all-channels', 'WebRTC\WebRTCController@getAllChannels');
    Route::post('connect-with-user', 'WebRTC\WebRTCController@connectWithUser');
    Route::post('accept-connection-request', 'WebRTC\WebRTCController@acceptConnectionRequest');
    Route::post('send-ice-candidate', 'WebRTC\WebRTCController@sendIceCandidate');
    Route::post('send-message', 'WebRTC\WebRTCController@sendMessage');
});

// EXAMPLE OF HOW TO USE SCOPES

//Route::get('/orders', function () {
//    // Access token has both "check-status" and "place-orders" scopes...
//})->middleware('scopes:check-status,place-orders');

//Route::get('/orders', function () {
//    // Access token has either "check-status" or "place-orders" scope...
//})->middleware('scope:check-status,place-orders');

//Route::get('/cool', function() {
//
//    $data = [
//        'event' => 'UserSignedUp',
//        'data' => [
//            'username' => 'JohnDoe'
//        ]
//    ];
//
//    Redis::publish('test-channel', json_encode($data));
//
//
//    // Redis::set('name', 'Kirk');
//    // return Redis::get('name');
//});

//Route::get('/event', function() {
//
//    $user = App\Models\User::find(1);
//
//    event(new \App\Events\ServerCreated($user));
//
//    // Redis::set('name', 'Kirk');
//    // return Redis::get('name');
//});

//Route::get('users', function() {
//    return App\Models\User::latest('id')->get();
//});
//
//Route::get('/redi', function() {
//    Redis::set('name', 'Kirk');
//    return Redis::get('name');
//});
//
//Route::get('/private-event', function() {
//
//    // Create a new fake user
//    $faker = Faker\Factory::create();
//
//    // Create a new user
//    $fakeUser = new App\Models\User();
//    $fakeUser->first_name = $faker->firstName();
//    $fakeUser->last_name  = $faker->lastName;
//    $fakeUser->email      = $faker->email;
//    $fakeUser->password   = Hash::make('111111');
//    $fakeUser->verified   = 1;
//    $fakeUser->save();
//
//    // Only this user will be able to subscribe to the event
//    $user = App\Models\User::find(1);
//
//    event(new \App\Events\MessageSent($user, $fakeUser));
//
//    // Redis::set('name', 'Kirk');
//    // return Redis::get('name');
//});

// Login Routes (OAuth Clients Included)
Route::group(['prefix' => '/administration'], function () {
    Route::post('users', 'Administration\AdminController@getUsers');
    Route::get('users/{user}', 'Administration\AdminController@getUser');

});

Route::group(['prefix' => '/login', 'middleware' => ['role:' . config('role.names.super_admin')]], function () {
//    Route::get('user1', function() {
//        return App\Models\User::latest('id')->get();
//    });
});