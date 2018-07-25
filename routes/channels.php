<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel(config('socket.channels.private.default') . '{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('message.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel(config('socket.channels.presence.default'), function ($user) {
    if ($user) {
        return [ 'id' => $user->id, 'first_name' => $user->first_name, 'last_name' => $user->last_name ];
    }
});