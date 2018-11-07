<?php

namespace App\Events\WebRTC;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class WebRTCEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $user;
    private $userIdOfFriend;
    private $message;
    private $type;

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return config('socket.events.default');
    }

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, $userIdOfFriend, $message, $type)
    {
        $this->user = $user;
        $this->userIdOfFriend = $userIdOfFriend;
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel(config('socket.channels.private.default') . $this->userIdOfFriend);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'event' => static::class,
            'data' => [
                'user' => $this->user,
                'type' => $this->type,
                'message' => $this->message
            ]
        ];
    }
}
