<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ThreadMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $userId;
    private $userWhoSentMessage;
    public $message;

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
    public function __construct($userId, Message $message, User $user)
    {
        $this->userId = $userId;
        $this->message = $message;
        $this->userWhoSentMessage = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel(config('socket.channels.private.default') . $this->userId);
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
                'message' => $this->message,
                'first_name' => $this->userWhoSentMessage->first_name,
                'last_name' => $this->userWhoSentMessage->last_name
            ]
        ];
    }
}
